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

store.dispatch('getProductList');
const loading = computed(()=> store.state.transactionList.loading);
const userData = computed(()=> store.state.user.data);
const systemSet = computed(()=> store.state.system);

const formData = ref([]);

const items = ref([
        {
          text: "Dashboard",
          href: "/"
        },
    ]);

watch(
  () => store.state.transactionDetails,
  (newVal, oldVal) => {
    formData.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: !!newVal.status,
    };
  }
);

if (route.params.id) {
    store.dispatch("getTransactionDetails", route.params.id)
}

</script>
<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div class="text-center" v-if="loading">
        <span>loading...</span>
    </div>
    <div v-else class="row"> 
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="invoice-title">
              <h4 class="float-end font-size-16">Transaction # {{formData.transaction_number}}</h4>
              <div class="mb-4">
                <img :src="systemSet.logo" alt="logo" height="20" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col-6">
                <address>
                  <strong>Customer:</strong>
                  <br />Walk-in
                
                </address>
              </div>
             <div class="col-6 text-end">
                <address>
                  <strong>Transaction Date:</strong>
                  <br />{{formData.created_at}}
                  <br />
                  <br />
                </address>
            </div>

            </div>
            <div class="p-2 mt-3">
              <h3 class="font-size-16">Order summary</h3>
            </div>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width: 70px;">No.</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, ind) in formData.items" :key="item.id">
                    <td>
                      {{ind += 1}}
                    </td>
                    <td>
                      {{item.brand_name}}
                    </td>
                    <td >{{systemSet.currency}} {{ item.selling_price }}</td>
                    <td>{{item.quantity}}</td>
                    <td class="text-end">{{systemSet.currency}} {{item.total}}</td>
                  </tr>

                 
                  
                   <tr>
                    <td colspan="4" class="border-0 text-end">
                      <strong>Item Count</strong>
                    </td>
                    <td class="border-0 text-end">
                      {{ formData.total_quantity }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="4" class="border-0 text-end">
                      <strong>Total</strong>
                    </td>
                    <td class="border-0 text-end">
                      <h4 class="m-0">{{systemSet.currency}} {{ formData.grand_total }}</h4>
                    </td>
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
      </div>
    </div>
    
     
  </Layout>
</template>