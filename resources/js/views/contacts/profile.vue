<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
import Stat from "../../components/widgets/stat.vue";

import { revenueChart } from "./data-profile";
import profile from "../../../images/profile-img.png";

import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router';
import { ref, onMounted, computed, watchEffect } from 'vue'

const store = useStore();
const router = useRouter()
const route = useRoute()

const userDetails = computed(()=> store.state.user.data);

const title = route.meta.title;

const items = [
  {
    text: "Home",
    href: "/"
  },
  {
    text: "Profile",
    active: true
  }
];

const statData = [
  {
    icon: "bx bx-check-circle",
    title: "Completed Projects",
    value: "125"
  },
  {
    icon: "bx bx-hourglass",
    title: "Pending Projects",
    value: "12"
  },
  {
    icon: "bx bx-package",
    title: "Total Revenue",
    value: "$36,524"
  }
];

const passwordForm = ref({});

watchEffect(()=> {
  passwordForm.value.id = userDetails.value.id;
})

const changePassword = ()=> {
  store.dispatch('changePassword', passwordForm.value)
 .then((res) => {
    store.commit("notify", {
      show: true,
      title: 'Success',
      icon: 'mdi-check-all',
      classV: 'alert-success show',
      message: res.data.message,
    });
    passwordForm.value = {};
  })
  .catch(err => {
    store.commit("alert", {
      show: true,
      title: 'Error',
      icon: 'mdi-block-helper',
      classV: 'alert-danger show',
      message: err.response.data.message,
    });
  });
}

const changeInfo = ()=> {
  store.dispatch('changeMyInfo', { ...userDetails.value })
 .then((res) => {
    store.commit("notify", {
      show: true,
      title: 'Success',
      icon: 'mdi-check-all',
      classV: 'alert-success show',
      message: res.data.message,
    });
    if(userDetails.value.image_url){
      delete userDetails.value.image_url;
    }
  })
  .catch(err => {
    store.commit("alert", {
      show: true,
      title: 'Error',
      icon: 'mdi-block-helper',
      classV: 'alert-danger show',
      message: err.response.data.message,
    });
  });
}

const fileInput = ref(null);

const openFileInput = () => {
    fileInput.value.click();
  };
  

const handleFileInputChange = (ev) => {
  const file = ev.target.files[0];
  const reader = new FileReader();
  reader.onload = () => {
    userDetails.value.image_url = reader.result;

    userDetails.value.image = reader.result;
    ev.target.value = "";
  };
  reader.readAsDataURL(file);
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
    <div class="row">
      <div class="col-xl-12">
        <div class="card overflow-hidden">
          <div class="bg-soft bg-primary ">
            <div class="row">
              <div class="col-10">
                <div class="text-primary p-3">
                  <!-- <h5 class="text-primary mb-3">Welcome !</h5> -->
                  <!-- <p>It will seem like simplified</p> -->
                </div>
              </div>
              <div class="col-2 align-self-end">
                <img :src="profile" alt class="img-fluid" />
              </div>
            </div>
          </div>
          <div class="card-body pt-0">

            <div class="row">
              <div class="col-sm-4">
                <div class="avatar-lg profile-user-wid mb-4">
                   <div class="avatar-container position-relative">
                      <img
                        height="100" 
                        width="100" 
                        :src="userDetails.image"
                        alt="profile image"
                        class="img-thumbnail rounded-circle cursor-pointer cover"
                        @click="openFileInput"
                        type="button"
                      />
                      <input
                        type="file"
                        ref="fileInput"
                        style="display: none"
                        @change="handleFileInputChange"
                      />
                      <i class="fas fa-pencil-alt edit-icon" type="button" @click="openFileInput"></i>
                    </div>
                </div>
                <h5 class="font-size-15 text-truncate">{{ userDetails.first_name }} {{ userDetails.middle_name }} {{ userDetails.last_name }}</h5>
                <p class="text-muted mb-0 text-truncate">{{ userDetails.role }}</p>
              </div>

              <div class="col-sm-8">
                <div class="pt-4">
                  <div class="row">
                    <div class="col-6">
                      <!-- <h5 class="font-size-15">125</h5>
                      <p class="text-muted mb-0">Products</p> -->
                    </div>
                    <div class="col-6">
                      <!-- <h5 class="font-size-15">$1245</h5>
                      <p class="text-muted mb-0">Orders</p> -->
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end card -->


        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Profile Settings</h4>
            <p class="card-title-desc">
            </p>
            <b-tabs content-class="p-3 text-muted">
              <b-tab active class="border-0">
                <template v-slot:title>
                  <span class="d-inline-block d-sm-none">
                    <i class="fas fa-list"></i>
                  </span>
                  <span class="d-none d-sm-inline-block">Personal Information</span>
                </template>
                <form @submit.prevent="changeInfo" method="post">
                  <div class="row">
                    <div class="table-responsive mb-0  col-xl-6">
                      <table class="table">
                        <tbody>
                          <tr>
                            <th scope="row">First Name :</th>
                            <td><b-form-input
                                  id="first_name"
                                  type='text'
                                  size="lg"
                                  placeholder="Enter your first name"
                                  v-model="userDetails.first_name"
                                ></b-form-input></td>
                          </tr>
                          <tr>
                            <th scope="row">Middle Name :</th>
                            <td><b-form-input
                                  id="middle_name"
                                  type='text'
                                  size="lg"
                                  placeholder="Enter your middle name"
                                  v-model="userDetails.middle_name"
                                ></b-form-input></td>
                          </tr>
                          <tr>
                            <th scope="row">Last Name :</th>
                            <td><b-form-input
                                  id="last_name"
                                  type='text'
                                  size="lg"
                                  placeholder="Enter your last name"
                                  v-model="userDetails.last_name"
                                ></b-form-input></td>
                          </tr>
                         
                        </tbody>
                      </table>
                    </div>
                    <div class="table-responsive mb-0 col-xl-6">
                      <table class="table">
                        <tbody>
                          <tr>
                            <th scope="row">Email :</th>
                            <td><b-form-input
                                  id="email"
                                  type='email'
                                  size="lg"
                                  placeholder="Enter your email"
                                  v-model="userDetails.email"
                                ></b-form-input></td>
                          </tr>
                          <tr>
                            <th scope="row">Address :</th>
                            <td>
                              <b-form-input
                                  id="address"
                                  type='text'
                                  size="lg"
                                  placeholder="Enter your address"
                                  v-model="userDetails.address"
                                ></b-form-input></td>
                          </tr>
                          <tr>
                            <th scope="row">Phone Number :</th>
                            <td><b-form-input
                                  id="phone_number"
                                  type='text'
                                  min='11'
                                  size="lg"
                                  placeholder="Enter your phone number"
                                  v-model="userDetails.phone_number"
                                ></b-form-input></td>
                          </tr>
                         
                        </tbody>
                         <tfoot>
                            <tr>
                              <td colspan="2"> <!-- Adjust colspan based on the number of columns in your table -->
                                <div class="d-flex justify-content-end mt-3">
                                    <input class="btn btn-danger" type="submit" value="Submit" />
                                </div>
                              </td>
                            </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </form>
              </b-tab>
              <b-tab>
                <template v-slot:title>
                  <span class="d-inline-block d-sm-none">
                    <i class="fa fa-key"></i>
                  </span>
                  <span class="d-none d-sm-inline-block">Change Password</span>
                </template>
                 <div>
                  <form @submit.prevent="changePassword">
                    <div class="table-responsive mb-0">
                      <table class="table">
                        <tbody>
                          <tr>
                            <th scope="row">Current Password :</th>
                            <td>
                               <b-form-input
                                id="current_password"
                                size="lg"
                                 type='password'
                                placeholder="Enter current password"
                                v-model="passwordForm.current_password"
                              ></b-form-input>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row">New Password :</th>
                            <td>
                               <b-form-input
                                id="password"
                                type='password'
                                size="lg"
                                placeholder="Enter new password"
                                v-model="passwordForm.password"
                              ></b-form-input>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row">Confirm Password :</th>
                            <td>
                               <b-form-input
                                id="password_confirmation"
                                size="lg"
                                type='password'
                                placeholder="Enter new password again"
                                v-model="passwordForm.password_confirmation"
                              ></b-form-input>
                            </td>
                          </tr>
                          
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="2"> <!-- Adjust colspan based on the number of columns in your table -->
                              <div class="d-flex justify-content-end mt-3">
                                  <input class="btn btn-danger" type="submit" value="Submit" />
                              </div>
                            </td>
                          </tr>
                        </tfoot>
                      </table>

                    </div>
                  </form>
                </div>
              </b-tab>
             
            </b-tabs>
          </div>
        </div>
      </div>
    </div>
    <!-- end row -->
  </Layout>
</template>