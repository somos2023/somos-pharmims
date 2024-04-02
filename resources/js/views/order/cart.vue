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

const emptyQty = ref(false);
const total = ref(0);

const deleteItem = (id) => {

   if (window.confirm("Are you sure you want to delete this item?")) {
      store.dispatch('deleteCartItem', id)
       .then((res) => {
        store.commit("notify", {
          show: true,
          title: 'Success',
          icon: 'mdi-check-all',
          classV: 'alert-success show',
          message: res.data.message,
        });
        // cartList.value.data = cartList.value.data.filter(item => item.id !== id);
        for (const supplierId in cartList.value.data) {
          if (cartList.value.data.hasOwnProperty(supplierId)) {
            // Filter products for the current supplier
            cartList.value.data[supplierId].products = cartList.value.data[supplierId].products.filter(item => item.id !== id);

            // If the products become empty, remove the supplier
            if (cartList.value.data[supplierId].products.length === 0) {
              delete cartList.value.data[supplierId];
            }
          }
        }
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

const updateItem = (qty, item) => {
 
  if(qty > item.stock){
    item.quantity = item.stock;
  } else if(qty < 1){
    item.quantity = 1;
  } else if(!qty){
    emptyQty.value = true;
  }
  
  if(item){
    store.dispatch('saveToCart', item)
    .then((res) => {
        
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

// const getTotal = ()=> {
//       return cartList.value.data.reduce((total, item) => {
//         return total + (item.price * item.quantity || 0); // Calculate subtotal for each item and sum up
//       }, 0);
//    }

const getTotal = () => {
  // Assuming cartList is a reactive property containing the provided data
  // const cartList = ref({
  //   data: {
  //     "3": {
  //       // ... supplier 3 data
  //     },
  //     "5": {
  //       // ... supplier 5 data
  //     },
  //     // ... other suppliers
  //   },
  // });

  // Iterate through the suppliers and their products to calculate the total
  return Object.values(cartList.value.data).reduce((total, supplier) => {
    return (
      total +
      supplier.products.reduce((supplierTotal, product) => {
        return supplierTotal + (product.price * product.quantity || 0);
      }, 0)
    );
  }, 0);
};

const checkout = ()=> {
   sessionStorage.removeItem("BUYNOW");
   store.commit("setCheckout", true); 
   router.push({name: "checkout"});
}

watchEffect(()=> {
    total.value = getTotal();
})

const fixValue = (num)=> {
  return num.toFixed(2);
}

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
      <div class="row">
         <div class="col-xl-8">
           <div class="card">
             <div v-if="total != 0" class="card-body">

              <div v-for="(group, supp_id) in cartList.data" :key="supp_id" class="mb-5">
                <div class="row mb-2"> 
                  <div class="col-lg-6">
                    <p class="mb-2">
                      Supplier: 
                      <span class="text-primary">{{group.supplier_name}}</span>
                    </p>
                  </div>
                  
                </div>

                <div class="table-responsive">
                 <table
                   class="table table-centered mb-0 table-nowrap align-middle"
                 >
                   <thead class="table-light">
                     <tr>
                       <!-- <th>Product</th> -->
                       <th>Product</th>
                       <th>Price</th>
                       <th>Quantity</th>
                       <th colspan="2">Total</th>
                     </tr>
                   </thead>
                   <tbody>
                     <tr v-for="item in group.products" :key="item.id">
                       <!-- <td>
                         <img
                           :src="item.product_image"
                           alt="product-img"
                           title="product-img"
                           class="avatar-md"
                         />
                       </td> -->
                       <td>
                         <h5 class="font-size-14 text-truncate">
                           <a href="/ecommerce/product-detail" class="text-dark"
                             >{{item.brand_name}}</a
                           >
                         </h5>
                         <p class="mb-0">
                           Unit of Measure :
                           <span class="fw-medium">{{item.packing}}</span>
                         </p>
                       </td>
                       <td>{{systemSet.currency}} {{ item.price }}</td>
                       <td>
                         <input
                           @change="updateItem(item.quantity = $event.target.value, item)"
                           type="number"
                           :value="item.quantity"
                           name="demo_vertical"
                            min="1"
                           :max="item.stock"
                           class="form-control"
                           style="width: 120px"
                         />
                       </td>
                       <td>{{systemSet.currency}} {{ fixValue(item.subtotal = item.price * item.quantity) }}</td>
                       <td>
                         <a
                           @click="deleteItem(item.id)"
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
                 <div class="col-sm-6">
                   <a href="/supplier-products" class="btn btn-secondary">
                     <i class="mdi mdi-arrow-left me-1"></i> Continue Shopping
                   </a>
                 </div>
                 <!-- end col -->
                 <div class="col-sm-6">
                   <div class="text-sm-end mt-2 mt-sm-0">
                     <a @click="checkout" class="btn btn-success" :class="{'disabled': emptyQty}">
                       <i class="mdi mdi-cart-arrow-right me-1"></i> Checkout
                     </a>
                   </div>
                 </div>
                 <!-- end col -->
               </div>
               <!-- end row-->
             </div>
           </div>
         </div>
         <div v-if="total != 0"  class="col-xl-4">
           
           <div class="card">
             <div class="card-body">
               <h4 class="card-title mb-3">Order Summary</h4>

               <div class="table-responsive">
                 <table class="table mb-0">
                   <tbody>
                     <tr>
                       <th>Total :</th>
                       <th>{{systemSet.currency}} {{ fixValue(total) }}</th>
                     </tr>
                   </tbody>
                 </table>
               </div>
               <!-- end table-responsive -->
             </div>
           </div>
           <!-- end card -->
         </div>
         
      </div>
      <div v-if="Object.keys(cartList.data).length === 0"  class="row d-flex justify-content-center">
        <div class="col-lg-9 "> 
        <div class="card">
         <div class="card-body">
           <div class="text-center">
                      No records
                    </div>
            <div class="row mt-4">
             <div class="col-sm-6">
               <a href="/supplier-products" class="btn btn-secondary">
                 <i class="mdi mdi-arrow-left me-1"></i> Continue Shopping
               </a>
             </div>
             <!-- end col -->
           </div>
         </div>
         </div>
       </div>
       </div>
   </div>
  </Layout>
</template>

