<script setup>

import { ref, computed, watch, onMounted,watchEffect } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

import Layout from "../layouts/main.vue";
import Profile from "../components/widgets/profile.vue";
import Earning from "../components/widgets/earning.vue";
import Stat from "../components/widgets/stat.vue";
import Transaction from "../components/widgets/transaction.vue";
import ColumnChart from "../components/widgets/column-chart.vue";
import CriticalStocks from "../components/widgets/critical-stocks.vue";
import ExpiredStocks from "../components/widgets/expired-products.vue";
import RadialBar from "../components/widgets/radial-bar.vue";
import ScatterChart from "../components/widgets/scatter-chart.vue";


const store = useStore();
const router = useRouter()
const route = useRoute()

const userData = computed(()=> store.state.user);
const checkA = computed(()=> store.state.isAdmin);
const checkB = computed(()=> store.state.isStaff);
const checkC = computed(()=> store.state.isSupplier);

const statData = computed(()=> store.state.dashboard.data.stats);

const criticalsData = computed(()=> store.state.dashboard.data.criticals);
const expiredData = computed(()=> store.state.dashboard.data.expired);
const highSelling = computed(()=> store.state.dashboard.data.highsells);
const lowSelling = computed(()=> store.state.dashboard.data.lowsells); 

const loading = computed(()=> store.state.dashboard.loading);

</script>
<template>
  <Layout>
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">{{route.meta.title}}</h4>

          <div class="page-title-right">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item active">{{ $t('page.header.text')}}</li>
            </ol>
          </div>
        </div>
      </div>
    </div> 
    <!-- end page title --> 
    <div class="text-center" v-if="loading">
        <span>loading...</span>
    </div>
    <div v-else class="row"> 
      <div v-if="userData.data.user_role != 3" v-for="stat of statData" :key="stat.icon" class="col-md-3 ">
        <router-link :to="{name: `${stat.link}`}" > 
          <Stat :icon="stat.icon" :label="stat.label" :number="stat.number"  :prefix="stat.prefix" class="shadow-sm cursor-pointer"/>
        </router-link>
      </div>
      <!-- <div v-else-if="userData.data.user_role != 3" v-for="stat of statData" :key="stat.icon" class="col-md-3 ">
       
        <Stat :icon="stat.icon" :label="stat.label" :number="stat.number"  :prefix="stat.prefix"/>
        
      </div> -->
      <div v-else-if="userData.data.user_role == 3" v-for="stat of statData" :key="stat.icon" class="col-md-6 " >
        <router-link :to="{name: `${stat.link}`}" > 
          <Stat :icon="stat.icon" :label="stat.label" :number="stat.number"  :prefix="stat.prefix"/>
        </router-link>
      </div>
      
      <div v-if="userData.data.user_role != 3" class="row">
        <div class="col-xl-6">
          <ColumnChart v-if="highSelling" :columnData="highSelling" cartTitle="High Selling Products" />
        </div>
        <div v-if="lowSelling" class="col-xl-6">
          <ColumnChart  v-if="lowSelling" :columnData="lowSelling" cartTitle="Low Selling Products" />
        </div>
      </div>
      <div v-if="userData.data.role_id != 3" class="row">
        <div class="col-xl-5"> 
          <CriticalStocks v-if="criticalsData" :listData="criticalsData"  /> 
        </div>
        <div class="col-xl-7"> 
          <ScatterChart v-if="expiredData" :scatterData="expiredData" cardTitle="Expired Product Stocks" /> 
        </div>
      </div>      
    </div>
  </Layout>
</template>
