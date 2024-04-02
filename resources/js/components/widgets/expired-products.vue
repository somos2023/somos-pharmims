<script setup>
import TableComponent from "./widgets-table.vue";

import { ref, computed, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

const props = defineProps({
    listData: {
      type: Array,
      default: []
    },
});

const columnData = [
  "order_number",
  "barcode",
  "brand_name",
  "quantity",
  "expiration_date",
  "created_at",
];

const selectOptions = [
  { id: 5, label: '5 per page' },
  { id: 15, label: '15 per page' },
  { id: 30, label: '30 per page' }
]
const perPage = ref(10)
const currentPage = ref(0)
const searchQuery = ref('');
const filteredItems = ref([]);
const pageList = ref([]);
const numPages = ref(7);
const numDisplayedPages = 5;

const calculateNumPages = computed(() => Math.ceil(props.listData.length / perPage.value));
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
    filteredItems.value = props.listData.filter((item) => {
        return (
          (item.barcode.toLowerCase().includes(searchQuery.value.toLowerCase()))
        );
    });

    numPages.value = Math.ceil(filteredItems.value.length / perPage.value);
    currentPage.value = 0;
    pageList.value = [];
    for (let i = 0; i < numPages.value; i++) {
        pageList.value.push(i);
    }
})


</script>

<style>
  table {
    table-layout: auto;
  }
</style>

<template>
  <div  class="card">
    <div class="card-body">
      <h4 class="mb-3">Expired Product Stocks</h4>
      <TableComponent v-if="servicesPaginated.length" :items='servicesPaginated' :columns="columnData" model="critical" :pages="pageList" :current="currentPage" :human="currentPageHuman" :num_pages="numPages" @change="changePage" />
      <div v-else>
        No Critical Stocks
      </div>
    </div>
  </div>
</template>