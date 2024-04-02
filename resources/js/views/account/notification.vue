<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import simplebar from "simplebar-vue";

import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router';
import { ref, onMounted, computed, watchEffect } from 'vue'

const store = useStore();
const router = useRouter()
const route = useRoute()

const notifList = computed(()=> store.state.notifList);

const title = route.meta.title;

const items = [
  {
    text: "Home",
    href: "/"
  },
  {
    text: "Notification",
    active: true
  }
];


const isOpen = ref(false);
const openedNotification = ref({});

const openModal =(data)=> {
  if(data.status == 'unopened') {
    store.dispatch('changenotIfStatus', data.id)
    .then(()=> {
      data.status = 'viewed'
    })
  }
  
  openedNotification.value = data
  isOpen.value = !isOpen.value

}

</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
    <div class="row d-flex justify-content-center">
      <div class="col-lg-9 "> 
        <div class="card ">
          <div class="card-body max-vh-100"> 
            <simplebar v-if="notifList.length > 0" style="max-height: 100vh;">
            <a href="javascript: void(0);" @click="openModal(item)" v-for="item in notifList" :key="item.id" class="text-reset " >
              <div class="d-flex py-4 px-2" :class="{'notification': item.status == 'unopened'}" style="border-bottom: 1px solid #EBEDEF;">
                <div class="avatar-xs me-3 ">
                  <span class="avatar-title bg-danger rounded-circle font-size-16">
                    <i class='bx bxs-hourglass'></i> 
                  </span>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mt-0 mb-1">{{item.message}}</h6>
                  <div class="font-size-12 text-gray" :class="{'text-white': item.status == 'unopened'}" >
                    <p class="mb-1">Order #: {{item.stock.order_number}}</p>
                    <p class="mb-1">Brand Name: {{item.product.brand_name}}</p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ item.date_time }} 
                    </p>
                  </div>
                </div>
              </div>
            </a>
          </simplebar>
          <div v-else>
            You don't have notifications yet
          </div>
          </div>
        </div>
      </div>
    </div>
    <b-modal v-model="isOpen"  size="md" centered title="Notification">
    <h5 class="mt- mb-3  text-primary">{{openedNotification.message}}</h5>
    <div v-if="Object.keys(openedNotification).length > 0" class="row">
      <div class="col-xl-6">
        <div class="product-detai-imgs">
          <div class="product-img">
            <img
              :src="openedNotification.product.image"
              alt
              class="img-fluid mx-auto d-block"
            />
          </div>
        </div>
      </div>
      <div class="col-xl-6">
        <div class="mt-3">
          <h4 class="mt- mb-1">{{openedNotification.product.brand_name}}</h4>
          <p class="text-muted mb-3 mt-3 float-left me-3">Barcode: {{openedNotification.stock.barcode}}</p>
          
          <div class="row mb-3">
            <div class="col-md-12">
              <div>
                <p class="text-muted mb-3 float-left me-3">
                  Order #: {{openedNotification.stock.order_number}}
                </p>
                <p class="text-muted">
                  Stock: {{openedNotification.stock.quantity}}
                </p>
                <p class="text-muted">
                  Expire Date : {{openedNotification.stock.expires_at}}
                </p>
                
              </div>
            </div>
          </div>
        </div>
      </div>
         <!-- {{openedNotification.product.image}} -->
    </div> 

     <template #footer>
          <div class="w-100"></div>
        </template>
  </b-modal>
    <!-- end row -->
  </Layout>
</template>

<style>
  .notification {
    background-color: #5DADE2 ;
    border-bottom: 2px solid #fff;
    color: white;
  }
</style>