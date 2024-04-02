<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
import TableComponent from "../../components/table-component.vue";


import { ref, computed, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()
const userData = computed(()=> store.state.user.data);
store.dispatch('getUserList');
const loading = computed(()=> store.state.userList.loading);
const listData = computed(()=> store.state.userList.data);


const columnData = [
        "role",
        "full_name",
        "email",
        "address",
        "phone_number",
        "status",
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
  { id: 30, label: '30 per page' }
]
const perPage = ref(5)
const currentPage = ref(0)
const searchQuery = ref('');
const selectedRole = ref('');
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
          (selectedRole.value === '' || item.role == selectedRole.value) &&
          (item.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.email.toLowerCase().includes(searchQuery.value.toLowerCase())  ||
            item.phone_number.includes(searchQuery.value.toLowerCase()) ||
            item.address.toLowerCase().includes(searchQuery.value.toLowerCase())  )
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

const roleOptions = [
  { id: '', label: 'All Roles' },
  { id: 'Admin', label: 'Admin' },
  { id: 'Staff', label: 'Staff' },
  { id: 'Supplier', label: 'Supplier' },
]


</script>
<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div class="text-center" v-if="loading">
        <span>loading...</span>
    </div>
    <div v-else> 
        <div class="row d-flex justify-content-between">
            <div class="col-md-5">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="show"
                      >Show</label
                    >
                    <select id="show" v-model="perPage" class="form-select form-control " >
                      <option v-for="opt in selectOptions" :key="opt.id" :value="opt.id">{{opt.label}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="show"
                      >Role</label
                    >
                    <select id="show" v-model="selectedRole" class="form-select form-control " >
                      <option v-for="opt in roleOptions" :key="opt.id" :value="opt.id">{{opt.label}}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="search"
                      >Search</label
                    >
                    <input id="search" v-model="searchQuery" type="text" class="form-control" placeholder="Search..." />
                </div>
            </div>
        </div>
        <TableComponent :roleID="userData.role_id" :hideActions="userData.role_id == 2" :items='servicesPaginated' :columns="columnData" :model="userData.role_id == 2 ? 'staff-user' : 'user'" :pages="pageList" :current="currentPage" :human="currentPageHuman" :num_pages="numPages" @change="changePage" />
    </div>
   
  </Layout>
</template>