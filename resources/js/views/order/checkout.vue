<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import { ref, computed, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

store.dispatch('getCart');
const loading = computed(()=> store.state.cartList.loading);
const cartList = computed(()=> store.state.cartList);
const systemSet = computed(()=> store.state.system);
const userData = computed(()=> store.state.user.data);
const checkoutState = computed(()=> store.state.checkout);

const checkoutForm = ref({});
const checkoutItems = ref([]);
let submitted = ref(false);
const errors = ref({});
const buyNowData = ref([]);
let hasBuyNow = ref(false);

const placeOrder = ()=> {
  submitted.value = true;
   if (window.confirm("Confirm Place Order")) {
        if(hasBuyNow.value){
            store.dispatch('saveBuyNow', checkoutForm.value)
           .then((res) => {
            submitted.value = false;
            errors.value = {};
            sessionStorage.removeItem("CHECKOUT");
            store.commit("setPlaceOrder", true);
            router.push({name: "place-order"})
          })
          .catch(err => {
            errors.value = err.response.data.errors;
          }); 
        } else {
            store.dispatch('saveOrder', checkoutForm.value)
           .then((res) => {
            submitted.value = false;
            errors.value = {};
            sessionStorage.removeItem("CHECKOUT");
            store.commit("setPlaceOrder", true);
            router.push({name: "place-order"})
          })
          .catch(err => {
            errors.value = err.response.data.errors;
          });
        }
   }
}

const getTotalQty = ()=> {
    // return checkoutItems.value.reduce((total, item) => {
    //     return total + (item.quantity || 0); // Calculate subtotal for each item and sum up
    // }, 0);
    return Object.values(checkoutItems.value).reduce((total, supplier) => {
        return (
          total +
          supplier.products.reduce((supplierTotal, product) => {
            return supplierTotal + ( product.quantity || 0);
          }, 0)
        );
      }, 0);
  }

const getTotal = ()=> {
    // return checkoutItems.value.reduce((total, item) => {
    //     return total + (item.price * item.quantity || 0); // Calculate subtotal for each item and sum up
    // }, 0);
    return Object.values(checkoutItems.value).reduce((total, supplier) => {
    return (
      total +
      supplier.products.reduce((supplierTotal, product) => {
        return supplierTotal + (product.price * product.quantity || 0);
      }, 0)
    );
  }, 0);
}

const fixValue = (num)=> {
  return num.toFixed(2);
}

const calculateSupplierTotal = (supplier) => {
  return supplier.products.reduce((total, product) => {
    return total + (product.price * product.quantity || 0);
  }, 0);
};

const calculateSupplierQuantity = (supplier) => {
  return supplier.products.reduce((total, product) => {
    return total + ( product.quantity || 0);
  }, 0);
};

const calculateTotals = () => {
  const cartListCopy = checkoutItems.value; 
  for (const supplierId in cartListCopy) {
    if (cartListCopy.hasOwnProperty(supplierId)) {
      const supplier = cartListCopy[supplierId];
      supplier.total = calculateSupplierTotal(supplier);
      supplier.quantity = calculateSupplierQuantity(supplier);
    }
  }

  return cartListCopy;
};

watchEffect(()=> {
 if(hasBuyNow.value){
    checkoutItems.value = buyNowData.value;
  } else {
    checkoutItems.value = cartList.value.data;
  }
  const totalQty = getTotalQty();
  const grand = getTotal();
  calculateTotals()
  
  checkoutForm.value = {
    name: userData.value.full_name,
    phone: userData.value.phone_number,
    address: userData.value.address,
    total_quantity: totalQty,
    grand_total: grand,
    item: checkoutItems.value
  };
})

// -- end

const items = ref([
  {
    text: "Dashboard",
    href: "/"
  },
]);

onMounted(()=> {
    const storedData = sessionStorage.getItem("BUYNOW");
    if (storedData) {
       buyNowData.value = JSON.parse(storedData);
       hasBuyNow.value = true;
    }
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
      <div class="row"> 
         <div class="checkout-tabs"> 
            <b-tabs pills vertical nav-wrapper-class="col-xl-2 col-sm-3" content-class="w-100">
                <b-tab active>
                    <template v-slot:title>
                        <i class="bx bxs-truck d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">Shipping Info</p>
                    </template>
                    <b-card-text>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Shipping information</h4>
                                <p class="card-title-desc">Fill all information below</p>
                                <form>
                                    <b-row class="mb-4">
                                        <b-col md="2">
                                            <label for="input-name">Name</label>
                                        </b-col>
                                        <b-col md="10">
                                            <b-form-input id="input-name" placeholder="Enter your name" v-model="checkoutForm.name"></b-form-input>
                                        </b-col>
                                    </b-row>

                                    <b-row class="mb-4">
                                        <b-col md="2">
                                            <label for="billing-phone">Phone</label>
                                        </b-col>
                                        <b-col md="10">
                                            <b-form-input id="billing-phone"
                                                placeholder="Enter your Phone no." maxlength="11" v-model="checkoutForm.phone" class="form-control" :class="{ 'is-invalid': submitted && errors.phone }"></b-form-input>
                                                <div v-if="submitted && errors.phone" class="invalid-feedback">
                                                <span>{{errors.phone[0]}}</span>
                                            </div>
                                        </b-col>
                                    </b-row>

                                    

                                    <b-row class="mb-4">
                                        <b-col md="2">
                                            <label for="billing-address">Address</label>
                                        </b-col>
                                        <b-col md="10">
                                            <b-form-textarea id="billing-address" rows="3"
                                                placeholder="Enter full address" v-model="checkoutForm.address"></b-form-textarea>
                                        </b-col>
                                    </b-row>


                                    <b-row class="mb-4">
                                        <b-col md="2">
                                            <label for="example-textarea">Order Notes:</label>
                                        </b-col>
                                        <b-col md="10">
                                            <b-form-textarea id="example-textarea" rows="3"
                                                placeholder="Write some note.." v-model="checkoutForm.note"></b-form-textarea>
                                        </b-col>
                                    </b-row>
                                </form>
                            </div>
                        </div>
                        <div class="row my-4">
                            <div class="col-sm-6">
                                <a href="/cart" class="btn text-muted d-none d-sm-inline-block btn-link">
                                    <i class="mdi mdi-arrow-left me-1"></i> Back to Shopping Cart
                                </a>
                            </div>
                            <!-- end col -->
                            <div class="col-sm-6">
                                <div class="text-sm-end">
                                  <a @click="placeOrder" class="btn btn-success">
                                     Place Order
                                  </a>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                    </b-card-text>
                </b-tab>
                <b-tab>
                    <template v-slot:title>
                        <i class="bx bx-money d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">Payment Info</p>
                    </template>
                    <b-card-text>
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h4 class="card-title">Payment information</h4>
                                    <!-- <p class="card-title-desc">Fill all information below</p> -->

                                    <div class="mt-3">
                                          
                                        <div class="custom-control custom-radio custom-control-inline me-4">
                                            <input id="customRadioInline3" type="radio" name="customRadioInline1"
                                                class="custom-control-input me-2" checked />
                                            <label class="custom-control-label" for="customRadioInline3">
                                                <i class="far fa-money-bill-alt me-1 font-size-20 align-top"></i> Cash on
                                                Delivery
                                            </label>
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                        </div>
                        <div class="row my-4">
                            <div class="col-sm-6">
                                <a href="/cart" class="btn text-muted d-none d-sm-inline-block btn-link">
                                    <i class="mdi mdi-arrow-left me-1"></i> Back to Shopping Cart
                                </a>
                            </div>
                            <!-- end col -->
                            <div class="col-sm-6">
                                <div class="text-sm-end">
                                  <a @click="placeOrder" class="btn btn-success">
                                     Place Order
                                  </a>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                    </b-card-text>
                </b-tab>
             <b-tab>
                    <template v-slot:title>
                        <i class="bx bx-badge-check d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">Confirmation</p>
                    </template>
                    <b-card-text>
                        <div class="card shadow-none border mb-0">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Order Summary</h4>
                                <div v-for="(group, supp_id) in checkoutItems" :key="supp_id" class="mt-4">
                                    <div class="row mb-2"> 
                                      <div class="col-lg-6">
                                        <p class="mb-2">
                                          Supplier: 
                                          <span class="text-primary">{{group.supplier_name}}</span>
                                        </p>
                                      </div>
                                      
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 table-nowrap">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">Image</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in group.products" :key="item.id">
                                                    <th scope="row">
                                                        <img :src="item.product_image" alt="product-img" title="product-img"
                                                            class="avatar-md" />
                                                    </th>
                                                    <td>
                                                        <h5 class="font-size-14 text-truncate">
                                                            <a href="/ecommerce/product-detail" class="text-dark">{{item.brand_name}}</a>
                                                        </h5>
                                                        <p class="text-muted mb-0">{{systemSet.currency}} {{item.price}} x {{item.quantity}}</p>
                                                    </td>
                                                    <td>{{systemSet.currency}} {{ fixValue(item.subtotal = item.price * item.quantity)  }}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="2">
                                                        <h6 class="m-0 text-end">Subtotal:</h6>
                                                    </td>
                                                    <td>{{systemSet.currency}} {{ group.total }} </td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-end">Grand Total ({{checkoutForm.total_quantity}}): </h6>
                                    </td>
                                    <td>{{systemSet.currency}} {{ fixValue(checkoutForm.grand_total)}}</td>
                                </tr>
                            </div>
                        </div>
                        <div class="row my-4">
                            <div class="col-sm-6">
                                <a href="/cart" class="btn text-muted d-none d-sm-inline-block btn-link">
                                <i class="mdi mdi-arrow-left me-1"></i> Back to Shopping Cart
                            </a>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a @click="placeOrder" class="btn btn-success">
                                   Place Order
                                </a>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                </b-card-text>
            </b-tab>   
          </b-tabs>
        </div>
      </div>
   </div>
  </Layout>
</template>

