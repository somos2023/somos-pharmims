<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
import TableComponent from "../../components/table-plain.vue";


import { ref, computed, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

const loading = computed(()=> store.state.salesReport.loading);
const listData = computed(()=> store.state.salesReport.data);
const salesTotal = computed(()=> store.state.salesReport.totals);

const checkA = computed(()=> store.state.isAdmin);
const checkB = computed(()=> store.state.isStaff);
const checkC = computed(()=> store.state.isSupplier);

if(checkC.value){
  store.dispatch('getOrderSales');
} else {
  store.dispatch('getTransactionSales');
}

const columnData = ref([])
const form = ref({});
let submitted = ref(false);
const errors = ref({});

const submitForm = ()=> {
  submitted.value = true;
  const actions = checkC.value ? 'getFilteredOrderSales' : 'getFilteredTransactionSales';
  store.dispatch(`${actions}`, form.value)
   .then((res) => {
      submitted.value = false;
      errors.value = {};
      
    })
    .catch(err => {
      errors.value = err.response.data.errors;
    });
}

const columnOrderData = [
  "order_number",
  "total_quantity",
  "total_amount",
  "ordered_by",
  "phone",
  "address",
  "status",
  "order_date",
];

const columnTransactionData = [
  "transaction_number",
  "total_quantity",
  "total_amount",
  "operated_by",
  "transaction_date",
];

const items = ref([
    {
      text: "Dashboard",
      href: "/"
    },
]);


const fixValue = (num)=> {
  return num.toFixed(2);
}

const selectOptions = [
  { id: 5, label: '5 per page' },
  { id: 15, label: '15 per page' },
  { id: 30, label: '30 per page' },
  { id: listData.value.length, label: 'All Records' },
]
const perPage = ref(5)
const currentPage = ref(0)
const searchQuery = ref('');
const filteredItems = ref([]);
const pageList = ref([]);
const numPages = ref(7);
const numDisplayedPages = 5;

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

const displayedPages = computed(() => {
  const totalPages = Math.min(numPages, numDisplayedPages);

  const pages = [];

  if (numPages <= numDisplayedPages) {
    for (let i = 0; i < numPages; i++) {
      pages.push(i);
    }
  } else if (currentPage <= Math.ceil(totalPages / 2)) {
    for (let i = 0; i < totalPages; i++) {
      pages.push(i);
    }
  } else if (currentPage >= numPages - Math.floor(totalPages / 2)) {
    for (let i = numPages - totalPages; i < numPages; i++) {
      pages.push(i);
    }
  } else {
    const startPage = currentPage - Math.floor(totalPages / 2);
    for (let i = startPage; i < startPage + totalPages; i++) {
      pages.push(i);
    }
  }

  return pages;
});

const servicesPaginated = computed(() => {
  return filteredItems.value.slice(perPage.value * currentPage.value, perPage.value * (currentPage.value + 1));
});

function changePage(newPage){
    currentPage.value = newPage;
}


watchEffect(()=> {
    filteredItems.value = listData.value.filter((item) => {
      return (
        (
          checkC.value  
            ? columnOrderData.some(column => item[column].toLowerCase().includes(searchQuery.value.toLowerCase()))
            : columnTransactionData.some(column => item[column].toLowerCase().includes(searchQuery.value.toLowerCase()))
        )
      );

    });

    numPages.value = Math.ceil(filteredItems.value.length / perPage.value);
    currentPage.value = 0;
    pageList.value = [];
    for (let i = 0; i < numPages.value; i++) {
        pageList.value.push(i);
    }

    columnData.value = checkC.value ? columnOrderData : columnTransactionData;
})

onMounted(()=> {
  items.value.push({
    text: route.meta.title,
    active: true
  });
})

// const replaceColName = (column)=> {
//   return column.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
// }

</script>

<style>
  table {
    table-layout: auto;
  }
</style>

<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div class="text-center" v-if="loading">
        <span>loading...</span>
    </div>
    <div v-else> 
        <div class="row d-flex justify-content-between">
            <div class="col-md-2">
                <div class="mb-3">
                    <label for="show"
                      >Show</label
                    >
                    <select id="show" v-model="perPage" class="form-select form-control " >
                      <option v-for="opt in selectOptions" :key="opt.id" :value="opt.id">{{opt.label}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5 ">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="start_date">Start Date</label>
                    <input id="start_date" v-model="form.start_date" type="date" class="form-control" placeholder="Enter start_date" :class="{ 'is-invalid': submitted && errors.start_date }" @change="submitForm" />
                    <div v-if="submitted && errors.start_date" class="invalid-feedback">
                        <span>{{errors.start_date[0]}}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="end_date">End Date</label>
                    <input id="end_date" v-model="form.end_date" type="date" class="form-control" placeholder="Enter end_date" :class="{ 'is-invalid': submitted && errors.end_date }" @change="submitForm"/>
                    <div v-if="submitted && errors.end_date" class="invalid-feedback">
                        <span>{{errors.end_date[0]}}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div> 
        <div v-if="form.start_date && form.end_date" class="card mb-3" >
          <div class="card-header bg-info text-white">
            Sales Between {{form.start_date}} and {{form.end_date}}
          </div>
          <div class="card-body">
            <div class="row ">
              <div class="table-responsive">
               <table class="table mb-0">
                 <tbody>
                   <tr>
                     <th>Total Revenue :</th>
                     <th>{{salesTotal.total_revenue}}</th>
                   </tr>
                   <tr>
                     <th>Total Units Sold :</th>
                     <th>{{salesTotal.total_units_sold}}</th>
                   </tr>
                   <tr>
                     <th>Average Transaction Value :</th>
                     <th>{{ fixValue(salesTotal.average_transaction_value) }}</th>
                   </tr>
                 </tbody>
               </table>
             </div>
            </div>
          </div>
        </div>
        <TableComponent :items='servicesPaginated' :columns="columnData" model="user" :pages="pageList" :current="currentPage" :human="currentPageHuman" :num_pages="numPages" @change="changePage" />
    </div>
   
  </Layout>
</template>