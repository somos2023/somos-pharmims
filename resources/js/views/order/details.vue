<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
import TableComponent from "../../components/table-component.vue";


import { ref, computed, watch, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

const userData = computed(()=> store.state.user.data);
const systemSet = computed(()=> store.state.system);

const order = ref([]);
const loading = ref(true);

const items = ref([
        {
          text: "Dashboard",
          href: "/"
        },
    ]);

watch(
  () => store.state.orderDetails,
  (newVal, oldVal) => {
    order.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: !!newVal.status,
    };
  }
);

if (route.params.id) {
    store.dispatch("getOrderDetails", route.params.id)
    .then(()=>{
      loading.value = false;
    })
}

</script>
<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div class="text-center" v-if="loading">
        <span>loading...</span>
    </div>
    <div v-else class="row d-flex justify-content-center"> 
      <div  class="col-lg-5">
        <div v-if="order.length != 0" class="card"> 
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
                      <!-- <th scope="col">Image</th> -->
                      <th scope="col">Product Name</th>
                      <th scope="col">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in order.items" :key="item.id">
                      <!-- <th scope="row">
                        <div>
                          <img :src="item.image" alt class="avatar-sm" />
                        </div>
                      </th> -->
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
                <div class="float-end">
                  <a href="javascript:window.print()" class="btn btn-success">
                    <i class="fa fa-print"></i>
                  </a>
                </div>
              </div>
            </div>
            
          </div>
        <div v-else class="card">
          <div class="card-body">
            <div class="text-center">
              Not Found
            </div>
          </div>
        </div>
      </div>
    </div>
    
     
  </Layout>
</template>