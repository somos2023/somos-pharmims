<script>

import profileImg from '../../../images/profile-img.png';
import logo from '../../../images/logo.svg';
import axios from 'axios';

/**
 * Register component
 */
export default {
  data() {
    return {
      auth: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      },
      profileImg, logo,
      processing: false,
      regError: null,
      isRegisterError: false,
    };
  },
  methods: {
    async register() {
      this.processing = true
      await axios.post('/api/register', this.auth).then(({ data }) => {
        console.log('data', data)
        if (data.success == true && data.message == 'success') {
          const logged_user = {
            login: true,
            user_id: data.data.id,
            name: data.data.name,
            email: data.data.email,
          }
          localStorage.setItem('user', JSON.stringify(logged_user));
          this.$router.push('/');
        } else {
          if(data.data == 400) {
            this.regError = data.message;
            this.isRegisterError = true;
          }
        }
      }).catch(({response:{data}}) => {
        console.log(data)
      }).finally(() => {
        this.processing = false
      })
    },
  }
};
</script>

<template>
  <div class="account-pages my-5 pt-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <div class="card overflow-hidden">
            <div class="bg-soft bg-primary">
              <div class="row">
                <div class="col-7">
                  <div class="text-primary p-4">
                    <h5 class="text-primary">Free Register</h5>
                    <p>Get your free Skote account now.</p>
                  </div>
                </div>
                <div class="col-5 align-self-end">
                  <img :src="profileImg" alt class="img-fluid" />
                </div>
              </div>
            </div>
            <div class="card-body pt-0">
              <div>
                <router-link to="/">
                  <div class="avatar-md profile-user-wid mb-4">
                    <span class="avatar-title rounded-circle bg-light">
                      <img :src="logo" alt class="rounded-circle" height="34" />
                    </span>
                  </div>
                </router-link>
              </div>
        
              <b-alert v-model="isRegisterError" class="mt-3" variant="danger" dismissible>{{ regError }}</b-alert>
    
              <b-form class="p-2" action="javascript:void(0)" method="POST">
                <slot />
                <b-form-group id="email-group" label="Name" label-for="name" class="mb-3">
                  <b-form-input id="name" v-model="auth.name" name="name" type="text" placeholder="Enter name" autocomplete="off"></b-form-input>
                </b-form-group>
    
                <b-form-group id="fullname-group" label="Email" label-for="email" class="mb-3">
                  <b-form-input id="email" name="email" v-model="auth.email" type="email" placeholder="Enter email" autocomplete="off"></b-form-input>
                </b-form-group>
    
                <b-form-group id="password-group" label="Password" label-for="password" class="mb-3">
                  <b-form-input id="password" v-model="auth.password" name="password" type="password"
                    placeholder="Enter password" autocomplete="off"></b-form-input>
                </b-form-group>
                <b-form-group label="Confirm Password" label-for="password-confirm" class="mb-3">
                  <b-form-input id="password-confirm" v-model="auth.password_confirmation" name="password_confirmation"
                    type="password" placeholder="Confirm password" autocomplete="off"></b-form-input>
                </b-form-group>
    
                <div class="mt-4 d-grid">
                  <b-button type="submit" :disabled="processing" @click="register" variant="primary" class="btn-block">
                    {{ processing ? "Please wait" : "Register" }}
                  </b-button>
                </div>
                <div class="mt-4 text-center">
                  <h5 class="font-size-14 mb-3">Sign in with</h5>
    
                  <ul class="list-inline">
                    <li class="list-inline-item">
                      <a href="javascript: void(0);" class="social-list-item bg-primary text-white border-primary">
                        <i class="mdi mdi-facebook"></i>
                      </a>
                    </li>
                    <li class="list-inline-item">
                      <a href="javascript: void(0);" class="social-list-item bg-info text-white border-info">
                        <i class="mdi mdi-twitter"></i>
                      </a>
                    </li>
                    <li class="list-inline-item">
                      <a href="javascript: void(0);" class="social-list-item bg-danger text-white border-danger">
                        <i class="mdi mdi-google"></i>
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="mt-4 text-center">
                  <p class="mb-0">
                    By registering you agree to the Skote
                    <a href="javascript: void(0);" class="text-primary">Terms of Use</a>
                  </p>
                </div>
              </b-form>
            </div>
            <!-- end card-body -->
          </div>
          <!-- end card -->
    
          <div class="mt-5 text-center">
            <p>
              Already have an account ?
              <router-link to="/login" class="fw-medium text-primary">Login</router-link>
            </p>
            <!-- <p>
              © {{ new Date().getFullYear() }} Skote. Crafted with
              <i class="mdi mdi-heart text-danger"></i> by Themesbrand
            </p> -->
          </div>
        </div>
        <!-- end col -->
      </div>
    </div>
  </div>
</template>

<style lang="scss" module></style>
