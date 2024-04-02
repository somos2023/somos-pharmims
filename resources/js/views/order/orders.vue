<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import { ref, computed} from '@vue/reactivity';
import { onMounted, watchEffect } from "vue"
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

store.dispatch('getMyOrder');
const loading = computed(()=> store.state.myOrders.loading);
const listData = computed(()=> store.state.myOrders.data);
const systemSet = computed(()=> store.state.system);
const sending = computed(()=> store.state.sending);
const toPayData = ref([]);
const toReceiveData = ref([]);
const receivedData = ref([]);

watchEffect(()=> {
  if (Array.isArray(listData.value)) {
    toPayData.value = listData.value.filter(item => item.status === "To pay");
    toReceiveData.value = listData.value.filter(item => item.status === "To receive");
    receivedData.value = listData.value.filter(item => item.status === "Received");
  } else {
    console.error("listData.value.data is not an array");
    toPayData.value = [];
    toReceiveData.value = [];
    receivedData.value = [];
  }
})

const changeOrder = (order, status)=> {
  store.dispatch("saveOrder", order)
  .then((res)=> {
    store.commit("notify", {
      show: true,
      title: 'Success',
      icon: 'mdi-check-all',
      classV: 'alert-success show',
      message: res.data.message,
    });
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
// -----

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
      <div class="col-lg-12"> 
        <!-- <div class="card">
          <div class="card-body"> -->
            <b-tabs pills justified content-class="p-3 text-muted">
              <b-tab active class="border-0">
                <template v-slot:title>
                  <span class="d-inline-block d-sm-none">
                    <i class="fas fa-file-invoice"></i>
                  </span>
                  <span class="d-none d-sm-inline-block">All</span>
                </template>
                <div v-if="listData.length != 0" v-for="order in listData" :key="order.id" class="mt-2" >
                  <div class="card">
                    <div class="card-body">
                      <div class="row mb-4"> 
                        <div class="col-lg-6">
                          <p class="mb-2">
                            Order Number:
                            <span class="text-primary">{{order.order_number}}</span>
                          </p>
                          <p v-if="order.status == 'To pay'" class="mb-2" >
                            Order Status:
                            <span class="text-info">{{order.status}}</span>
                          </p>
                          <p v-else-if="order.status == 'To receive'" class="mb-2 " >
                            Order Status:
                            <span class="text-warning">{{order.status}}</span>
                          </p>
                          <p v-else-if="order.status == 'Received'" class="mb-2 " >
                            Order Status:
                            <span class="text-success">{{order.status}}</span>
                          </p>
                          <p v-if="order.name" class="mb-2" >
                            Order By:
                            <span class="text-primary">{{order.name}}</span>
                          </p>
                        </div>
                        <div class="col-lg-6">
                          <p  v-if="order.phone" class="mb-2">
                            Phone Number:
                            <span class="text-primary">{{order.phone}}</span>
                          </p>
                          <p v-if="order.address" class="mb-2" >
                            Address:
                            <span class="text-primary">{{order.address}}</span>
                          </p>
                          <p v-if="order.created_at" class="mb-2" >
                            Order Date:
                            <span class="text-primary">{{order.created_at}}</span>
                          </p>
                         
                        </div>
                        
                      </div>
                      
                      
                      <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                          <thead>
                            <tr>
                              <th scope="col">Product</th>
                              <th scope="col">Product Name</th>
                              <th scope="col">Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in order.items" :key="item.id">
                              <th scope="row">
                                <div>
                                  <img :src="item.image" alt class="avatar-sm" />
                                </div>
                              </th>
                              <td>
                                <div>
                                  <h5 class="text-truncate font-size-14">
                                    {{item.brand_name}} ({{item.packing}})
                                  </h5>
                                  <p class="text-muted mb-0">{{systemSet.currency}} {{item.price}} x {{item.quantity}}</p>
                                </div>
                              </td>
                              <td>{{systemSet.currency}} {{item.subtotal}}</td>
                            </tr>
                            
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Sub Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Shipping:</h6>
                              </td>
                              <td>Free</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="d-print-none">
                        <div class="float-start">
                          <router-link :to="`/order-details/${order.id}`" class="btn btn-success"><i class="fa fa-print"></i></router-link>
                        </div>
                      </div>
                    </div>
                    <div v-if="order.status == 'To receive'" class="card-footer">
                      <div class="d-flex justify-content-end ">
                        <button class="btn btn-danger" @click="changeOrder(order, order.status = 'Received')">Received</button>
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
              </b-tab>
              <b-tab>
                <template v-slot:title>
                  <span class="d-inline-block d-sm-none">
                     <i class="fa fa-wallet"></i>
                  </span>
                  <span class="d-none d-sm-inline-block">To Pay</span>
                </template>
                <div v-if="toPayData.length != 0" v-for="order in toPayData" :key="order.id" class="mt-2" >
                  <div class="card">
                    <div class="card-body">
                      <div class="row mb-4"> 
                        <div class="col-lg-6">
                          <p class="mb-2">
                            Order Number:
                            <span class="text-primary">{{order.order_number}}</span>
                          </p>
                          <p  v-if="order.phone" class="mb-2">
                            Phone Number:
                            <span class="text-primary">{{order.phone}}</span>
                          </p>
                        </div>
                        <div class="col-lg-6">
                          <p v-if="order.address" class="mb-2" >
                            Address:
                            <span class="text-primary">{{order.address}}</span>
                          </p>
                         
                        </div>
                      </div>
                      
                      <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                          <thead>
                            <tr>
                              <th scope="col">Product</th>
                              <th scope="col">Product Name</th>
                              <th scope="col">Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in order.items" :key="item.id">
                              <th scope="row">
                                <div>
                                  <img :src="item.image" alt class="avatar-sm" />
                                </div>
                              </th>
                              <td>
                                <div>
                                  <h5 class="text-truncate font-size-14">
                                    {{item.brand_name}} ({{item.packing}})
                                  </h5>
                                  <p class="text-muted mb-0">{{systemSet.currency}} {{item.price}} x {{item.quantity}}</p>
                                </div>
                              </td>
                              <td>{{systemSet.currency}} {{item.subtotal}}</td>
                            </tr>
                            
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Sub Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Shipping:</h6>
                              </td>
                              <td>Free</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="d-print-none">
                        <div class="float-start">
                          <router-link :to="`/order-details/${order.id}`" class="btn btn-success"><i class="fa fa-print"></i></router-link>
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
              </b-tab>
              <b-tab>
                <template v-slot:title>
                  <span class="d-inline-block d-sm-none">
                    <i class="fas fa-truck"></i>
                  </span>
                  <span class="d-none d-sm-inline-block">To Receive</span>
                </template>
                <div v-if="toReceiveData.length != 0" v-for="order in toReceiveData" :key="order.id" class="mt-2" >
                  <div class="card">
                    <div class="card-body">
                      <div class="row mb-4"> 
                        <div class="col-lg-6">
                          <p class="mb-2">
                            Order Number:
                            <span class="text-primary">{{order.order_number}}</span>
                          </p>
                          <p  v-if="order.phone" class="mb-2">
                            Phone Number:
                            <span class="text-primary">{{order.phone}}</span>
                          </p>
                        </div>
                        <div class="col-lg-6">
                          <p v-if="order.address" class="mb-2" >
                            Address:
                            <span class="text-primary">{{order.address}}</span>
                          </p>
                         
                        </div>
                      </div>
                      
                      <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                          <thead>
                            <tr>
                              <th scope="col">Product</th>
                              <th scope="col">Product Name</th>
                              <th scope="col">Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in order.items" :key="item.id">
                              <th scope="row">
                                <div>
                                  <img :src="item.image" alt class="avatar-sm" />
                                </div>
                              </th>
                              <td>
                                <div>
                                  <h5 class="text-truncate font-size-14">
                                    {{item.brand_name}} ({{item.packing}})
                                  </h5>
                                  <p class="text-muted mb-0">{{systemSet.currency}} {{item.price}} x {{item.quantity}}</p>
                                </div>
                              </td>
                              <td>{{systemSet.currency}} {{item.subtotal}}</td>
                            </tr>
                            
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Sub Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Shipping:</h6>
                              </td>
                              <td>Free</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="d-print-none">
                        <div class="float-start">
                          <router-link :to="`/order-details/${order.id}`" class="btn btn-success"><i class="fa fa-print"></i></router-link>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="d-flex justify-content-end ">
                        <button class="btn btn-danger" @click="changeOrder(order, order.status = 'Received')">Received</button>
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
              </b-tab>
              <b-tab>
                <template v-slot:title>
                  <span class="d-inline-block d-sm-none">
                    <i class="fas fa-box"></i>
                  </span>
                  <span class="d-none d-sm-inline-block">Received</span>
                </template>
                <div v-if="receivedData.length != 0" v-for="order in receivedData" :key="order.id" class="mt-2" >
                  <div class="card">
                    <div class="card-body">
                      <p class="mb-2">
                        Order Number:
                        <span class="text-primary">{{order.order_number}}</span>
                      </p>
                      
                      <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                          <thead>
                            <tr>
                              <th scope="col">Product</th>
                              <th scope="col">Product Name</th>
                              <th scope="col">Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="item in order.items" :key="item.id">
                              <th scope="row">
                                <div>
                                  <img :src="item.image" alt class="avatar-sm" />
                                </div>
                              </th>
                              <td>
                                <div>
                                  <h5 class="text-truncate font-size-14">
                                    {{item.brand_name}} ({{item.packing}})
                                  </h5>
                                  <p class="text-muted mb-0">{{systemSet.currency}} {{item.price}} x {{item.quantity}}</p>
                                </div>
                              </td>
                              <td>{{systemSet.currency}} {{item.subtotal}}</td>
                            </tr>
                            
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Sub Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Shipping:</h6>
                              </td>
                              <td>Free</td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <h6 class="m-0 text-end">Total:</h6>
                              </td>
                              <td>{{systemSet.currency}} {{order.grand_total}}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="d-print-none">
                        <div class="float-start">
                          <router-link :to="`/order-details/${order.id}`" class="btn btn-success"><i class="fa fa-print"></i></router-link>
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
              </b-tab>
            </b-tabs>
          <!-- </div>
        </div> -->
      </div>
    </div>

   
  </Layout>
</template>

