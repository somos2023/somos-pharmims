<script setup>
import { ref, onMounted, computed, watchEffect } from 'vue'
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore()
const route = useRoute()
const sysInfo = computed(() => store.state.system);

</script>

<template >
   <nav  class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm bg-light"style="z-index: 99999;" id="mainNav">
      <div class="container px-5">
        <router-link :to="{name: 'home'}">
         <div style="height: 50px; width: 120px" class="d-flex justify-content-lg-between align-items-center">
            <img class="h-75 mr-3" :src="sysInfo.logo">
            <a class="navbar-brand fw-bold" href="#mainNav">SOMOS</a>  
          </div>
        </router-link>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
              Menu
              <i class="bi-list"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
              <ul class="navbar-nav ms-auto me-4 my-3 my-lg-0">
                  <li class="nav-item"  ><router-link :to="{name: 'home'}" class="nav-link me-lg-3" :class="{'text-primary border-bottom border-primary': route.name == 'home'}" >HOME</router-link></li>
                  <!-- <li class="nav-item"><a class="nav-link me-lg-3" href="#download">Download</a></li> -->
              </ul>
              <router-link :to="{name: 'user-selection'}" class="btn btn-primary rounded-2 px-3 mb-2 mb-lg-0">
                  <span class="d-flex align-items-center">
                      <span class="small">Login</span>
                  </span>
              </router-link>
          </div>
      </div>
  </nav>

  <router-view :info="sysInfo" />

  <footer class="bg-black text-center py-5">
    <div class="container px-5">
      <div class="text-white-50 ">
        <div class="mb-2">&copy; {{sysInfo.name}} {{ new Date().getFullYear() }}.</div>
        <!-- <a href="#!">Privacy</a> -->
        <!-- <span class="mx-1">&middot;</span> -->
        <!-- <router-link :to="{name: 'terms'}">Terms</router-link>
        <span class="mx-1">&middot;</span>
        <router-link :to="{name: 'faq'}">FAQ</router-link> -->
      </div>
    </div>
  </footer>

  
</template>

