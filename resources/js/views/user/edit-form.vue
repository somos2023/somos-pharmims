<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import { ref, computed, watchEffect, watch, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

// const users = computed(()=> store.state.userList.data);


const items = ref([
        {
          text: "Dashboard",
          href: "/"
        },
    ]);

const form = ref({
    password: 'User123',
    password_confirmation: 'User123'
});
let submitted = ref(false);
const errors = ref({});
let update = ref(false);
let passwordType = ref('password');

watch(
  () => store.state.currentUser,
  (newVal, oldVal) => {
    form.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: !!newVal.status,
    };
  }
);

if (route.params.id) {
    update.value = true;
    store.dispatch("getCurrentUser", route.params.id)
}

const userForm = ()=> {
    store.commit('setSending', true)
    submitted.value = true;
    store.dispatch('saveUser', form.value)
     .then((res) => {
        store.commit('setSending', false)
        store.commit("notify", {
          show: true,
          title: 'Success',
          icon: 'mdi-check-all',
          classV: 'alert-success show',
          message: res.data.message,
        });
        submitted.value = false;
        errors.value = {};

        if(!update.value){
            form.value = {};
            form.value = {
                password: 'User123',
                password_confirmation: 'User123'
            };
        } else {
            router.push({name: "user-list"})
        }
        
      })
      .catch(err => {
        errors.value = err.response.data.errors;
        store.commit('setSending', false)
      });
}

const showpass = ()=> {
    if(passwordType.value == 'password'){
        passwordType.value = 'text'
    } else {
        passwordType.value = 'password'
    }
}

onMounted(()=> {
    items.value.push({
      text: route.meta.title,
      active: true
    });
})
</script>
<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div class="row d-flex justify-content-center">
        <div class="col-lg-9 "> 
            <div class="card ">
                <div class="card-body"> 
                    <h4 class="card-title mb-3">User Details</h4>
                    <form class="needs-validation" @submit.prevent="userForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="first_name">First name <span class="text-danger">*</span></label>
                                    <input id="first_name" v-model="form.first_name" type="text" class="form-control" placeholder="First name"  :class="{ 'is-invalid': submitted && errors.first_name }" />
                                    <div v-if="submitted && errors.first_name" class="invalid-feedback">
                                        <span>{{errors.first_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="middle_name">Middle name</label>
                                    <input id="middle_name" v-model="form.middle_name" type="text" class="form-control" placeholder="Last name" :class="{ 'is-invalid': submitted && errors.middle_name }" />
                                    <div v-if="submitted && errors.middle_name" class="invalid-feedback">
                                        <span>{{errors.middle_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="last_name">Last name <span class="text-danger">*</span></label>
                                    <input id="last_name" v-model="form.last_name" type="text" class="form-control" placeholder="Last name" :class="{ 'is-invalid': submitted && errors.last_name }" />
                                    <div v-if="submitted && errors.last_name" class="invalid-feedback">
                                        <span>{{errors.last_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Address">Address</label>
                                    <input id="Address" v-model="form.address" type="text" class="form-control" placeholder="Enter your valid address" :class="{ 'is-invalid': submitted && errors.address }" />
                                    <div v-if="submitted && errors.address" class="invalid-feedback">
                                        <span>{{errors.address[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Phone">Phone</label>
                                    <input id="Phone" maxlength="11" v-model="form.phone_number" type="text" class="form-control" placeholder="Enter your phone number" :class="{ 'is-invalid': submitted && errors.phone_number }" />
                                    <div v-if="submitted && errors.phone_number" class="invalid-feedback">
                                        <span>{{errors.phone_number[0]}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-9 ">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title  mb-3">Account Details</h4>
                    <form class="needs-validation" @submit.prevent="userForm">
                        <div class="row">
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_role"
                                      >User Role <span class="text-danger">*</span></label
                                    >
                                    <select v-model="form.user_role" id="user_role" class="form-select form-control "  :class="{ 'is-invalid': submitted && errors.user_role }"  >
                                      <option value="">Choose...</option>
                                      <option value="1">Admin</option>
                                      <option value="2">Staff</option>
                                      <option value="3">Supplier</option>
                                    </select>
                                    <div v-if="submitted && errors.user_role" class="invalid-feedback">
                                        <span>{{errors.user_role[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input id="email" v-model="form.email" type="text" class="form-control" placeholder="Enter your email" :class="{ 'is-invalid': submitted && errors.email }" />
                                    <div v-if="submitted && errors.email" class="invalid-feedback">
                                        <span>{{errors.email[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!update" class="col-md-4">
                                <div class="mb-3">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input id="password" v-model="form.password" :type="passwordType" class="form-control" placeholder="Enter your password" :class="{ 'is-invalid': submitted && errors.password }" />
                                    <div v-if="submitted && errors.password" class="invalid-feedback">
                                        <span>{{errors.password[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!update" class="col-md-6">
                                <div class="mb-3">
                                    <label for="ConfirmPassword">Confirm Password <span class="text-danger">*</span></label>
                                    <input id="ConfirmPassword" v-model="form.password_confirmation" :type="passwordType" class="form-control" placeholder="Confirm your password" :class="{ 'is-invalid': submitted && errors.password_confirmation }" />
                                    <div v-if="submitted && errors.password_confirmation" class="invalid-feedback">
                                        <span>{{errors.password_confirmation[0]}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!update"  class="form-check mb-3">
                            <input
                              class="form-check-input"
                              type="checkbox"
                              id="show"
                              @change="showpass"
                            />
                            <label class="form-check-label" for="show">
                             Show Password
                            </label>
                        </div>
                        <button class="btn btn-primary mt-2" type="submit">{{ update ? 'Update' : 'Submit'}}</button>
                    </form>
                </div>
            </div>
        </div>
     
    </div>
  </Layout>
</template>