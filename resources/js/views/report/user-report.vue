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

store.dispatch('getUserList');
const loading = computed(()=> store.state.userList.loading);
const listData = computed(()=> store.state.userList.data);

const columnData = [
  "role",
  "first_name",
  "middle_name",
  "last_name",
  "email",
  "address",
  "phone_number",
  "status",
  "created_at",
];


const items = ref([
        {
          text: "Dashboard",
          href: "/"
        },
    ]);

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

const dateRange = [new Date(), new Date()] // Default date range values
// const minDate = new Date( /* your minimum date here */ )
// const maxDate = new Date( /* your maximum date here */ )

function handleDateRangeChange() {
      // Filter your table data based on the date range
      // const filteredData = this.yourTableData.filter(item => {
      //   const date = new Date(item.yourDateProperty); // Adjust based on your table structure
      //   return date >= this.dateRange[0] && date <= this.dateRange[1];
      // });

      // console.log(filteredData)
      // Update your table data with the filtered data
      // this.tableData = filteredData; // Uncomment this line if your tableData is reactive
    }


watchEffect(()=> {
    filteredItems.value = listData.value.filter((item) => {
        return (
          (item.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           item.email.toLowerCase().includes(searchQuery.value.toLowerCase()))
        );
    });

    numPages.value = Math.ceil(filteredItems.value.length / perPage.value);
    currentPage.value = 0;
    pageList.value = [];
    for (let i = 0; i < numPages.value; i++) {
        pageList.value.push(i);
    }
})

onMounted(()=> {
  items.value.push({
    text: route.meta.title,
    active: true
  });
})

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
            <div class="col-md-4 ">
              <div class="row">
                <div class="col-md-12">
                  <div class="mb-3">
                      <label for="search"
                        >Search</label
                      >
                      <input id="search" v-model="searchQuery" type="text" class="form-control" placeholder="Search..." />
                  </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                       
                    </div>
                </div>
              </div>
            </div>
        </div>
        <TableComponent :items='servicesPaginated' :columns="columnData" model="user" :pages="pageList" :current="currentPage" :human="currentPageHuman" :num_pages="numPages" @change="changePage" />
    </div>
   
  </Layout>
</template>