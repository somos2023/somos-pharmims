<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import profile from "../../../images/profile-img.png";
import defaultProfile from "../../../images/default-sys-logo.jpg";

import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router';
import { ref, onMounted, computed, watchEffect } from 'vue'

const store = useStore();
const router = useRouter()
const route = useRoute()

const systemData = computed(()=> store.state.system);

const title = route.meta.title;

const items = [
  {
    text: "Home",
    href: "/"
  },
  {
    text: "Settings",
    active: true
  }
];

let submitted = ref(false);
const errors = ref({});

const changeInfo = ()=> {
  submitted.value = true
  store.dispatch('saveSystemInfo', { ...systemData.value })
 .then((res) => {
    store.commit("notify", {
      show: true,
      title: 'Success',
      icon: 'mdi-check-all',
      classV: 'alert-success show',
      message: res.data.message,
    });
    submitted.value = false
    errors.value = {}

    if(systemData.value.logo_url){
      delete systemData.value.logo_url;
    }
  })
  .catch(err => {
    errors.value = err.response.data.errors;
    // store.commit("alert", {
    //   show: true,
    //   title: 'Error',
    //   icon: 'mdi-block-helper',
    //   classV: 'alert-danger show',
    //   message: err.response.data.message,
    // });
  });
}

const changeIcon = (ev) => {
  const file = ev.target.files[0];
  const reader = new FileReader();
  reader.onload = () => {
    systemData.value.icon = reader.result;

    systemData.value.icon_url = reader.result;
  };
  reader.readAsDataURL(file);
}
  

const changeLogo = (ev) => {
  const file = ev.target.files[0];
  const reader = new FileReader();
  reader.onload = () => {
    systemData.value.logo_url = reader.result;

    systemData.value.logo = reader.result;
  };
  reader.readAsDataURL(file);
}

const changeLogoLg = (ev) => {
  const file = ev.target.files[0];
  const reader = new FileReader();
  reader.onload = () => {
    systemData.value.logo_lg_url = reader.result;

    systemData.value.logo_lg = reader.result;
  };
  reader.readAsDataURL(file);
}

const changeCover = (ev) => {
  const file = ev.target.files[0];
  const reader = new FileReader();
  reader.onload = () => {
    systemData.value.cover_url = reader.result;

    systemData.value.cover = reader.result;
  };
  reader.readAsDataURL(file);
}
 
const isEdit = ref(false);

function toggleEdit(){
  isEdit.value = !isEdit.value;
}

</script>

<style>
  .avatar-container {
    position: relative;
    display: inline-block;
  }

  .edit-icon {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: white;
    border-radius: 50%;
    padding: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

</style>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
    <div class="row ">
      <div class="col-xl-12">
        
        <!-- end card -->
        <div  class="card">
          <form @submit.prevent="changeInfo" method="post">
          <div class="card-body">
              <div class="container px-5 py-24 mx-auto mb-5">
                <div class="flex flex-col text-center w-full mb-12">
                  <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">App Details</h1>
                </div>
               
              </div>
              <div class="row">
                <div class="col-md-6 justify-content-row">
                  <div class="mb-3">
                    <label for="name">App Name <span class="text-danger">*</span></label>
                    <input id="name" v-model="systemData.name" type="text" class="form-control" placeholder="App name" :class="{ 'is-invalid': submitted && errors.name }" />
                    <div v-if="submitted && errors.name" class="invalid-feedback">
                        <span>{{errors.name[0]}}</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="title">App Title <span class="text-danger">*</span></label>
                    <input id="title" v-model="systemData.title" type="text" class="form-control" placeholder="App title" :class="{ 'is-invalid': submitted && errors.title }" />
                    <div v-if="submitted && errors.title" class="invalid-feedback">
                        <span>{{errors.title[0]}}</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="currency">App Currency <span class="text-danger">*</span></label>
                    <input id="currency" v-model="systemData.currency" type="text" class="form-control" placeholder="App currency" :class="{ 'is-invalid': submitted && errors.currency }" />
                    <div v-if="submitted && errors.currency" class="invalid-feedback">
                        <span>{{errors.currency[0]}}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 justify-content-row">
                  <div class="mb-3">
                    <label for="logo_url">Logo</label>
                    <input id="logo_url"  type="file" class="form-control" placeholder="App logo" :class="{ 'is-invalid': submitted && errors.logo_url }" @change="changeLogo" />
                    <div v-if="submitted && errors.logo_url" class="invalid-feedback">
                        <span>{{errors.logo_url[0]}}</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="logo_lg_url">Logo 2</label>
                    <input id="logo_lg_url" type="file" class="form-control" placeholder="App logo 2" :class="{ 'is-invalid': submitted && errors.logo_lg_url }" @change="changeLogoLg"/>
                    <div v-if="submitted && errors.logo_lg_url" class="invalid-feedback">
                        <span>{{errors.logo_lg_url[0]}}</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="icon_url">Icon</label>
                    <input id="icon_url" type="file" class="form-control" placeholder="App icon" :class="{ 'is-invalid': submitted && errors.icon_url }" @change="changeIcon" />
                    <div v-if="submitted && errors.icon_url" class="invalid-feedback">
                        <span>{{errors.icon_url[0]}}</span>
                    </div>
                  </div>
                </div>
                


              </div>
            
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
    <!-- end row -->
  </Layout>
</template>