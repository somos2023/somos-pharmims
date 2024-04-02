<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import { ref, computed, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';



const store = useStore();
const router = useRouter()
const route = useRoute()

store.dispatch('getSupplierProducts');
const loading = computed(()=> store.state.supplierProductList.loading);
const listData = computed(()=> store.state.supplierProductList.data);
const systemSet = computed(()=> store.state.system);
const checkoutState = computed(()=> store.state.checkout);

const category = ref({ id: 1, label: "Medicine"});
const categoryList = [
    { id: 1, label: "Medicine"},
  ];
const isOpen = ref(false);
const isOpenDetail = ref(false);
const currentProduct = ref({});

const perPage = ref(5)
const currentPage = ref(0)
const searchFilter = ref('');
const filteredItems = ref([]);
const pageList = ref([]);
const numPages = ref(1);
const numDisplayedPages = 5;

const displayedPages = computed(() => {
  const totalPages = Math.min(numPages.value, numDisplayedPages);

  const pages = [];

  if (numPages.value <= numDisplayedPages) {
    for (let i = 0; i < numPages.value; i++) {
      pages.push(i);
    }
  } else if (currentPage.value <= Math.ceil(totalPages / 2)) {
    for (let i = 0; i < totalPages; i++) {
      pages.push(i);
    }
  } else if (currentPage.value >= numPages.value - Math.floor(totalPages / 2)) {
    for (let i = numPages.value - totalPages; i < numPages.value; i++) {
      pages.push(i);
    }
  } else {
    const startPage = currentPage.value - Math.floor(totalPages / 2);
    for (let i = startPage; i < startPage + totalPages; i++) {
      pages.push(i);
    }
  }

  return pages;
});

const calculateNumPages = computed(() => Math.ceil(listData.value.length / perPage.value));
numPages.value = calculateNumPages;
const currentPageHuman = computed(() => currentPage.value + 1)

const pagesList = computed(() => {
  const pagesList = []
  for (let i = 0; i < numPages.value; i++) {
    pagesList.push(i)
  }
  return pagesList
})

const servicesPaginated = computed(() => {
  return filteredItems.value.slice(perPage.value * currentPage.value, perPage.value * (currentPage.value + 1));
});

function changePage(newPage){
  currentPage.value = newPage;
}

const setCurrentProduct = (item)=> {
  item.quantity = 1 ;
  currentProduct.value = item;
  isOpen.value = true;
}

const viewProduct = (item)=> {
  currentProduct.value = item;
  isOpenDetail.value = true;
}

const buyNowData = ref({});
const checkout = (item) => {
  const data = {};
  data[currentProduct.value.supplier_id] = {
      supplier_id: currentProduct.value.supplier_id,
      supplier_name: currentProduct.value.supplier_name,
      supplier_image: currentProduct.value.supplier_image,
      products: [currentProduct.value]
    };
  buyNowData.value = data;

  store.commit("setBuyNow", buyNowData.value); 
  store.commit("setCheckout", true); 
  router.push({name: "checkout"})
}

const addToCart = (item) => {
  store.dispatch("saveToCart", {product: item.id})
  .then((res)=> {
    store.commit("notify", {
      show: true,
      title: 'Success',
      icon: 'mdi-check-all',
      classV: 'alert-success show',
      message: res.data.message,
    });
  })
  
}

const changeQuantity = ()=> {
  if(currentProduct.value.quantity > currentProduct.value.stock){
    currentProduct.value.quantity = currentProduct.value.stock;
  } else if(currentProduct.value.quantity < 0){
    currentProduct.value.quantity = 1;
  }
}

const openChat = (contact)=> {
  store.commit("setCurrentReceiver", contact.supplier_id);
  router.push({name: 'chat'})
}

watchEffect(()=> {
    filteredItems.value = listData.value.filter((item) => {
        return (
          (item.brand_name.toLowerCase().includes(searchFilter.value.toLowerCase()) ||
           item.generic_name.toLowerCase().includes(searchFilter.value.toLowerCase()))
        );
    });

    numPages.value = Math.ceil(filteredItems.value.length / perPage.value);
    currentPage.value = 0;
    pageList.value = [];
    for (let i = 0; i < numPages.value; i++) {
        pageList.value.push(i);
    }
})

// -- end

const items = ref([
  {
    text: "Dashboard",
    href: "/"
  },
]);

onMounted(()=> {
  items.value.push({
    text: route.meta.title,
    active: true
  });
})

</script>
<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div class="text-center" v-if="loading">
        <span>loading...</span>
    </div>
    <div v-else> 
      <div>
        <b-modal v-model="isOpen" size="lg" centered title="Buy Now!">
          <div class="row">
              <div class="col-xl-6">
                <div class="product-detai-imgs">
                  <b-tabs pills vertical nav-wrapper-class="col-md-2 col-sm-3 col-4">
                    <b-tab>
                      <template v-slot:title>
                        <img
                          :src="currentProduct.image"
                          alt
                          class="img-fluid mx-auto d-block tab-img rounded"
                        />
                      </template>
                      <div class="product-img">
                        <img
                          :src="currentProduct.image"
                          alt
                          class="img-fluid mx-auto d-block"
                        />
                      </div>
                    </b-tab>
                  </b-tabs>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="mt-3">
                  <h4 class="mt-1 mb-1">{{currentProduct.brand_name}}</h4>
                  <p class="text-muted mb-3 float-left me-3">{{currentProduct.generic_name}}</p>

                  <h5 class="mb-4">
                    Price :
                    <b>{{systemSet.currency}}{{ currentProduct.price }}</b>
                  </h5>
                  <p
                    class="text-muted mb-4"
                  >{{currentProduct.description}}</p>
                  <div class="row mb-3">
                    <div class="col-md-6">
                     <div>
                        <p class="text-muted">
                          
                          Dosage : {{currentProduct.formulation}}
                        </p>
                        <p class="text-muted">
                          Unit :  {{currentProduct.packing}}
                        </p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div>
                        <p class="text-muted">
                          Stock: {{currentProduct.stock}}
                        </p>
                        <p class="text-muted">
                          Expire Date : {{currentProduct.expiration_date}}
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="product-color mt-5">
                    <h5 class="font-size-15">Quantity :</h5>
                    <b-form-input
                      @blur="changeQuantity"
                      id="curr_quantity"
                      size=""
                      min="1"
                      :max="currentProduct.stock"
                      v-model="currentProduct.quantity"
                      type='number'
                    
                    ></b-form-input>
                    
                  </div>
                </div>
              </div>
            </div>
          <template #footer>
            <div class="w-100">
              <div class="text-sm-end mt-sm-0">
                <button @click="checkout" class="btn btn-success" :class="{'disabled': !currentProduct.quantity}">
                  <i class="mdi mdi-cart-arrow-right me-1"></i> Buy Now
                </button>
              </div>
            </div>
          </template>
        </b-modal>
      </div>

      <div>
        <b-modal hide-footer v-model="isOpenDetail" size="lg" centered title="Product Details">
          <div class="row">
              <div class="col-xl-6">
                <div class="product-detai-imgs">
                  <b-tabs pills vertical nav-wrapper-class="col-md-2 col-sm-3 col-4">
                    <b-tab>
                      <template v-slot:title>
                        <img
                          :src="currentProduct.image"
                          alt
                          class="img-fluid mx-auto d-block tab-img rounded"
                        />
                      </template>
                      <div class="product-img">
                        <img
                          :src="currentProduct.image"
                          alt
                          class="img-fluid mx-auto d-block"
                        />
                      </div>
                    </b-tab>
                  </b-tabs>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="mt-3">
                  <h4 class="mt-1 mb-1">{{currentProduct.brand_name}}</h4>
                  <p class="text-muted mb-3 float-left me-3">{{currentProduct.generic_name}}</p>

                  <h5 class="mb-4">
                    Price :
                    <b>{{systemSet.currency}} {{ currentProduct.price }}</b>
                  </h5>
                  <p
                    class="text-muted mb-4"
                  >{{currentProduct.description}}</p>
                  <div class="row mb-3">
                    <div class="col-md-6">
                     <div>
                        <p class="text-muted">
                          
                          Dosage : {{currentProduct.formulation}}
                        </p>
                        <p class="text-muted">
                          Unit :  {{currentProduct.packing}}
                        </p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div>
                        <p class="text-muted">
                          Stock: {{currentProduct.stock}}
                        </p>
                        <!-- <p class="text-muted">
                          Expire Date : {{currentProduct.expiration_date}}
                        </p> -->
                      </div>
                    </div>
                    <span class="text-muted mb-1 mt-4">Supplier: </span>
                    <div class=" col-md-10 d-flex py-3 border-top">

                      <img
                        :src="currentProduct.supplier_image"
                        class="avatar-xs me-3 rounded-circle"
                        alt="img"
                      />
                      <div class="flex-grow-1">
                        <h5 class="mt-0 font-size-15">{{currentProduct.supplier_name}}</h5>
                        <ul class="list-inline float-sm-end">
                          <li class="list-inline-item">
                            <a href="javascript: void(0);" @click="openChat(currentProduct)">
                              <i class="far fa-comment-dots me-1"></i> Chat
                            </a>
                          </li>
                        </ul>
                        <div v-if="currentProduct.supplier_phone_number" class="text-muted">
                          <i class="fa fa-phone text-primary me-1"></i> {{currentProduct.supplier_phone_number}}
                        </div>
                        <div class="text-muted">
                          <i class="fa fa-envelope text-primary me-1"></i> {{currentProduct.supplier_email}}
                        </div>
                        <!-- <div class="text-muted">
                          <router-link :to="{name: 'profile'}" >
                            <i class="far fa-eye text-primary me-1"></i>view profile
                          </router-link>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </b-modal>
      </div>
      <div class="row d-flex justify-content-center"> 
        <!-- <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Category</h4>
                    <div>
                      <ul class="list-unstyled">
                        <li v-for="data of categoryList" :key="data.id"
                          :class="{ active: category.id == data.id }">
                          <a href="javascript: void(0);">
                            <div class="d-flex">
                              <h5 class="font-size-14 mb-3">{{data.label}}</h5>
                            </div>
                          </a>
                        </li>
                      </ul>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="col-lg-9">
            <div class="row mb-3">
                <div class="col-xl-4 col-sm-6">
                    <div class="mt-2">
                        <h5>{{category.label}}</h5>
                    </div>
                </div>
                <div class="col-lg-8 col-sm-6">
                  <form class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                        <div class="search-box me-2">
                            <div class="position-relative">
                                <input type="text" class="form-control border-0" placeholder="Search..." v-model="searchFilter"  />
                                <i class="bx bx-search-alt search-icon"></i>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

              <div class="row" v-if="servicesPaginated.length"> 
                <div v-for="item in servicesPaginated" :key="item.id" class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="product-img position-relative">
                                <a href="#"  @click="viewProduct(item)" >
                                    <img :src="`${item.image}`" alt class="img-fluid mx-auto d-block" />
                                </a>
                            </div>
                            <div class="mt-4 text-center mb-4">
                                <h5 class="mb-3 text-truncate">
                                     <a href="#"  @click="viewProduct(item)" class="text-dark">{{ item.brand_name }}</a>
                                </h5>
                                <h5 class="my-0">
                                    <b>{{systemSet.currency}}{{ item.price }}</b>
                                </h5>

                            </div>
                            <div v-if="item.stock != 0" class="d-flex justify-content-between flex-wrap gap-2">
                              <b-button  @click="setCurrentProduct(item)" variant="primary" class="w-full col-lg-5">
                                Buy Now
                              </b-button>
                              <b-button @click="addToCart(item)" variant="light" class="w-full col-lg-5">
                                 Add To Cart
                              </b-button>
                              
                            </div>
                              <div  v-else  class="text-center w-full col-lg-12 py-1">
                                <h4 class="text-danger">Out of Stock</h4>
                              </div>
                              
                        </div>
                    </div>
                </div>
              </div>
              <div v-else class="card">
                <div class="card-body">
                 <div class="text-center">
                    No records
                  </div>
                </div>
              </div>
            <!-- end row -->

            <div v-if="servicesPaginated.length" class="row">
                <!-- <div class="col-lg-12">
                    <b-pagination v-if="servicesPaginated.length > 0" class="justify-content-center" pills v-model="currentPage" :total-rows="servicesPaginated.length" :per-page="5" aria-controls="my-table"></b-pagination>
                </div> -->
            </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

