<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import { ref, computed, watchEffect, onMounted, onBeforeUnmount } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

store.dispatch("getProductList");
const products = computed(() => store.state.productList.data);
const transactionData = computed(()=> store.state.currentTransaction);
const userData = computed(()=> store.state.user.data);
const systemSet = computed(()=> store.state.system);

const hasAction = ref(false);
const disabledAdd = ref(true);
const selectedProduct = ref({
  quantity: 0
});

const form = ref({});
const formItems = ref([]);

const searchQuery = ref('');
const itemsPerPage = 1; // Number of items to display per page
const currentPage = 1; // Current page
const filteredItems = ref([]);

const paginatedItems = computed(()=> {
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const newitem = filteredItems.value.slice(startIndex, endIndex);
  selectedProduct.value.item = newitem[0];
  return newitem;
});

const getTotalQty = ()=> {
    return formItems.value.reduce((total, item) => {
        return total + (item.quantity || 0); // Calculate subtotal for each item and sum up
    }, 0);
}

const getTotal = ()=> {
    return formItems.value.reduce((total, item) => {
        return total + (item.price * item.quantity || 0); // Calculate subtotal for each item and sum up
    }, 0);
}

watchEffect(()=> {

  if(searchQuery.value != ''){
    filteredItems.value = products.value.filter((user) => {
      return (
        (user.barcode.toLowerCase().includes(searchQuery.value.toLowerCase()))
      );
    });
  } else {
    filteredItems.value = [];
  }

  if(selectedProduct.value.item && selectedProduct.value.item.stock != 0){
    disabledAdd.value = false;
    selectedProduct.value.quantity = 1;
    const checkItem = computed(()=> formItems.value.find(item => item.product_id == selectedProduct.value.item.id));
    if(checkItem.value && checkItem.value.quantity == selectedProduct.value.item.stock){
       disabledAdd.value = true;
    }

  } else {
    disabledAdd.value = true;
    selectedProduct.value.quantity = 0;
  }

  const grand = getTotal();
  const totalQty = getTotalQty();

  form.value = {
    name: userData.value.first_name+" "+userData.value.last_name,
    items: formItems.value,
    total_quantity: totalQty,
    grand_total: grand,
  };
  
});


const saveTransaction = ()=> {
   if (window.confirm("Confirm To Submit")) {
    store.dispatch('saveTransaction', form.value)
     .then((res) => {
      store.commit("notify", {
        show: true,
        title: 'Success',
        icon: 'mdi-check-all',
        classV: 'alert-success show',
        message: res.data.message,
      });
      sessionStorage.removeItem("TRANSACTION");
      form.value = {};
      formItems.value = [];
      searchQuery.value = "";
      store.dispatch("getProductList");
    })
    .catch(err => {
      store.commit("alert", {
        show: true,
        title: 'Error',
        icon: 'mdi-block-helper',
        classV: 'alert-danger show',
        message: err.response.data.message,
      });
    });
  }
}

function toggleEdit() {
  hasAction.value = !hasAction.value;
}

function deleteItem(id){
  formItems.value = formItems.value.filter((item) => item.product_id != id);
  store.commit("setTransaction", formItems.value);
}

const updateAmount = (total)=> {
  return total.toFixed(2);
}

const fixValue = (num)=> {
  return num.toFixed(2);
}

const searchInput = ref(null);

const handleSpacebar = (event) => {
  if (event.key === ' ') {
    searchInput.value.focus();
    searchQuery.value = '';
    event.preventDefault();
  }

};

const addSubmit = ()=> {
  let selectedProductID = selectedProduct.value.item.id;
  let selectedQuantity = selectedProduct.value.quantity;
  let selectedPrice = selectedProduct.value.item.price;
  let total = selectedProduct.value.price * selectedQuantity;
  const item = {
    product_id: selectedProductID,
    barcode: selectedProduct.value.item.barcode,
    brand_name: selectedProduct.value.item.brand_name,
    stock: selectedProduct.value.item.stock,
    price: selectedPrice,
    quantity: selectedQuantity,
    total: total
  }

  const itemToUpdate = formItems.value.find(item => item.product_id === selectedProductID);
  if (itemToUpdate) {
    const totalQty = itemToUpdate.quantity + selectedQuantity;
    itemToUpdate.quantity = totalQty;
    itemToUpdate.total = updateAmount(selectedPrice * totalQty);
    store.commit("setTransaction", formItems.value);
  } else {
    formItems.value.push(item);
    store.commit("setTransaction", formItems.value);
  };

  searchInput.value.focus();
  searchQuery.value = ''

}

const changeFormQty = (qty, item) => {
  if(qty > item.stock){
    item.quantity = item.stock;
  } else if(qty < 1){
    item.quantity = 1;
  } 
  store.commit("setTransaction", formItems.value);
}

const changeSelectedQty = (qty, item) => {
  if(qty > item.stock){
    selectedProduct.value.quantity = item.stock;
  } else if(qty < 1){
    selectedProduct.value.quantity = 1;
  } else if(!qty){
    disabledAdd.value = true;
  }
}

// -- end

const items = ref([
  {
    text: "Dashboard",
    href: "/"
  },
]);


onMounted(()=> {
  const storedData = sessionStorage.getItem("TRANSACTION");
  if (storedData) {
     formItems.value = JSON.parse(storedData);
  }
  items.value.push({
    text: route.meta.title,
    active: true
  });

  window.addEventListener('keydown', handleSpacebar);
});

onBeforeUnmount(() => {
  // Remove the spacebar event listener when the component is destroyed
  window.removeEventListener('keydown', handleSpacebar);
});

</script>
<template>
  <Layout>
   <PageHeader :title="route.meta.title" :items="items" />
<!--    <div class="text-center" v-if="loading">
      <span>loading...</span>
   </div>
   <div v-else> -->
      <div class="row">
         <div class="col-xl-7">
           <div class="card">
             <div class="card-body">
              <div class="mb-5">
                <div class="row mb-2"> 
                  <div class="col-lg-6">
                    <p class="mb-2">
                      Staff: 
                      <span v-if="form.name" class="text-primary">{{form.name}}</span>
                    </p>
                  </div>
                </div>

                <div class="table-responsive">
                 <table
                   class="table table-centered mb-0 table-nowrap align-middle"
                 >
                   <thead class="table-light">
                     <tr>
                      
                       <th>Product</th>
                       <th>Price</th>
                       <th>Quantity /Piece</th>
                       <th colspan="2">Total</th>
                     </tr>
                   </thead>
                   <tbody>
                    <tr v-for="item in form.items" :key="item.id">
                      <td>
                         <h5 class="font-size-14 ">
                           <a @click="viewProduct" class="text-dark"
                             >{{item.brand_name}}</a
                           >
                         </h5>
                       </td>
                       <td>{{systemSet.currency}} {{ item.price }}</td>
                       <td>
                         <input
                            @change="changeFormQty(item.quantity = Number($event.target.value), item)"
                            type="number"
                            :value="item.quantity"
                            name="demo_vertical"
                            min="1"
                            :max="item.stock"
                            class="form-control"
                            style="width: 120px"
                         />
                       </td>
                       <td><span>{{systemSet.currency}} {{ fixValue(item.total = item.price * item.quantity) }}</span></td>
                       <td v-if="hasAction">
                         <a
                           @click="deleteItem(item.product_id)"
                           href="javascript:void(0);"
                           class="action-icon text-danger"
                         >
                           <i class="mdi mdi-trash-can font-size-18"></i>
                         </a>
                       </td>
                     </tr>
                     
                   </tbody>
                 </table>
                </div>
              </div>
              <div class="row mt-4">
                <div class="table-responsive">
                 <table class="table mb-0">
                   <tbody>
                     <tr>
                       <th>Item Count :</th>
                       <th>{{form.total_quantity}}</th>
                     </tr>
                     <tr>
                       <th>Grand Total :</th>
                       <th>{{systemSet.currency}} {{ fixValue(form.grand_total)}}</th>
                     </tr>
                   </tbody>
                 </table>
               </div>
             </div> 
               <div class="row d-flex justify-content-between">
                  <div class="col-md-6">
                   <div v-if="form.grand_total != 0" class=" mt-2 mt-sm-0">
                     <a @click="toggleEdit" class="btn " :class="[ hasAction ? 'btn-outline-danger': 'btn-light' ]" >
                      Edit
                     </a>
                   </div>
                 </div>
                 <!-- end col -->
                 <div class="col-md-6">
                   <div class="text-sm-end mt-2 mt-sm-0">
                     <a @click="saveTransaction" class="btn btn-success" :class="{'disabled': form.grand_total == 0}">
                      Submit
                     </a>
                   </div>
                 </div>
                 <!-- end col -->
               </div>
               <!-- end row-->
             </div>
             
           </div>
         </div>
         <div  class="col-xl-5">
           
           <div class="card">
             <div class="card-body">
               <h4 class="card-title mb-3">Order Summary</h4>

               <div class="table-responsive">
                 <table class="table mb-0">
                   <tbody>
                    <tr>
                      <th><h3>Total</h3></th>
                      <th><h3>{{systemSet.currency}} {{ fixValue(form.grand_total)}}</h3></th>
                    </tr>
                   </tbody>
                 </table>
               </div>

               <div class=" mt-0">
                <!-- <h4>Result:</h4> -->
              </div>
              <table
              v-if="searchQuery!= ''"
                   class="table table-centered mb-0 table-nowrap align-middle"
                 >
                   <thead class="table-light">
                     <tr>
                      
                       <th>Product Desc</th>
                       <th>Price</th>
                       <th>Stock</th>
                     </tr>
                   </thead>
                   <tbody>
                    <tr v-if="paginatedItems.length" v-for="item in paginatedItems" :key="item.id">
                      <td>
                        <h5 class="font-size-14 ">
                         <a @click="viewProduct" class="text-dark"
                           >{{item.brand_name}}</a
                         >
                        </h5>
                      </td>
                      <td>{{systemSet.currency}} {{ item.price }}</td>
                      <td>
                        <span v-if="item.stock != 0">{{ item.stock }}</span>
                        <span v-else class="text-danger">OUT OF STOCK</span>
                      </td>
                    </tr>
                    <tr v-else>
                      <td colspan="3">
                        <p>
                         Product not found
                        </p>
                      </td>
                    </tr>
                     
                   </tbody>
                 </table>
               
              <form ref="addForm" @submit.prevent="addSubmit">
                <div class="row d-flex justify-content-between mt-3">
                  <div class="col-md-12">
                      <div class="mb-3">
                          <label for="search"
                            >Enter/Scan</label
                          >
                          <div class="d-flex col text-center">
                          <svg xmlns="http://www.w3.org/2000/svg" width="60" height="38" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M5 7h2v10H5zm9 0h1v10h-1zm-4 0h3v10h-3zM8 7h1v10H8zm8 0h3v10h-3z"></path><path d="M4 5h4V3H4c-1.103 0-2 .897-2 2v4h2V5zm0 16h4v-2H4v-4H2v4c0 1.103.897 2 2 2zM20 3h-4v2h4v4h2V5c0-1.103-.897-2-2-2zm0 16h-4v2h4c1.103 0 2-.897 2-2v-4h-2v4z"></path></svg>&nbsp;
                          <input autofocus="true" v-model="searchQuery" type="text" class="form-control" placeholder="Barcode" ref="searchInput" />
                          </div>
                      </div>
                  </div>
                  <!-- <div class="col-md-5">
                      <div class="mb-3">
                          <label for="show"
                            >Enter Quantity</label
                          >
                          <input
                            @change="changeSelectedQty($event.target.value, selectedProduct.item)"
                            v-model="selectedProduct.quantity" type="number" min="0" :max="paginatedItems[0] ? paginatedItems[0].stock : 0" class="form-control" placeholder="Quantity" />
                      </div>
                  </div> -->
                
                </div>
                <div class="d-grid gap-2">
                  <b-button type="submit" block variant="outline-primary" :disabled="disabledAdd" size="lg"
                    >Add Item</b-button>
                </div>
              </form>
              

             </div>
           </div>
           <!-- end card -->
         </div>
      </div>
   <!-- </div> --> 
  </Layout>
</template>

