<script setup>
import { useStore } from "vuex";
import { useRouter, useRoute } from "vue-router";
import { ref, computed, onMounted, watchEffect} from "vue";
import profileImg from '../../../images/profile-img.png';
import logo from '../../../images/logo.svg';

const store = useStore()
const router = useRouter()
const route = useRoute()

const role = route.params.role.charAt(0).toUpperCase() + route.params.role.slice(1);

store.dispatch('getSystemInfo');
const sysInfo = computed(() => store.state.system);
const loginAttempt = computed(() => store.state.loginAttempt);

const auth = ref({
  email: sessionStorage.getItem("EMAIL") ?? '',
  password: "",
  role: route.params.role
});

// const attempData = ref({});

let isAuthError = ref(false);
const authError = ref(null);
let processing = ref(false);
const countdown = ref(null);
const showPassword = ref(false);

const startCountdown = (interval)=> {
    countdown.value = interval; // Convert milliseconds to seconds
    const timer = setInterval(() => {
      countdown.value--;
      store.commit('countdownLogin', countdown.value);
      store.dispatch('updateCountdown', loginAttempt.value);
      if (countdown.value <= 0) {
        clearInterval(timer);
        store.commit('disableLogin', false);
        store.dispatch('saveLoginAttempt', loginAttempt.value);
        sessionStorage.removeItem("EMAIL");
      } 
    }, 1000); // Update the countdown every second
  };


function login() {
  processing.value = true;
  store
    .dispatch("login", auth.value)
    .then(({data}) => {
      processing.value = false;
      // console.log(data.user[0].id)
      const dataSet = {
          id: data.user[0].id,
          status: 'active'
        };
      store.commit('setIsRole', data.user[0].role_id)
      store.dispatch('changeUserStatus', dataSet);
      store.dispatch('getUser').then(()=> {
        router.push('/dashboard');
      });
    })
    .catch((err) => {
      isAuthError.value = true;
      processing.value = false;
      authError.value = err.response.data.errors;

      if(err.response.data.message == 'not match' || err.response.data.message == 'locked'){
        store.commit("setLoginAttempt", err.response.data.login_attempt);
      }

      if(err.response.status == 422 && err.response.data.message == 'not match'){
        store.commit('addLoginError');
        store.dispatch('saveLoginAttempt', loginAttempt.value);
        let count = loginAttempt.value.count;
        if(count % 3 === 0){
          isAuthError.value = false;
          authError.value = null;
          store.commit('disableLogin', true);
          store.dispatch('saveLoginAttempt', loginAttempt.value);
          let interval = store.state.interval_time / 1000;
          startCountdown(interval);
        } 
        
      }
    });
}

onMounted(()=> {
  if(auth.value.email){
    store.dispatch('getLoginAttempt', auth.value.email)
    .then(()=> {
      if(loginAttempt.value.disabled){
        startCountdown(loginAttempt.value.countdown)
      }
    })
  }
})

</script>

<template>
  <div class="home-btn d-none d-sm-block">
    <router-link :to="{name: 'home'}" class="text-dark">
      <i class="fas fa-home h2"></i>
    </router-link>
  </div>
  <div class="account-pages my-5 pt-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <div class="card overflow-hidden">
            <div class="bg-soft bg-primary">
              <div class="row">
                <div class="col-7">  
                  <router-link :to="{name: 'user-selection'}">
                    <b-button pill variant="link"><i class="fa fa-arrow-left m-2"></i></b-button>
                  </router-link>
                  <div class="text-primary p-4 pt-1">

                    <h5 class="text-primary">Welcome {{role}}!</h5>
                    <p>Sign in to continue to {{sysInfo.name}}.</p>
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
                      <img :src="sysInfo.logo" alt height="34" />
                    </span>
                  </div>
                </router-link>
              </div>
              <div v-if="loginAttempt.disabled" class="text-center text-danger">
                <div class="row "><h3>Login Temporarily Disabled</h3></div> 
                <div class="row"><h2>{{countdown}}</h2></div>
              </div>

              <b-alert v-model="isAuthError" variant="danger" class="mt-3" dismissible>
                <div v-for="(field, i) of Object.keys(authError)" :key="i">
                  <li v-for="(error, ind) of authError[field] || []" :key="ind">{{ error }}</li>
                </div>
              </b-alert>

              <b-form @submit.prevent='login' class="p-2" method="POST">
                <slot />
                <b-form-group id="input-group-1" label="Email" label-for="input-1" class="mb-3">
                  <b-form-input @change="getAttempt()" :disabled="loginAttempt.disabled" id="input-1" name="email" v-model="auth.email" type="text"
                    placeholder="Enter email" autocomplete="off" ></b-form-input>
                </b-form-group>

                <b-form-group id="input-group-2" label="Password" label-for="input-2" class="mb-3">
                  <b-form-input :disabled="loginAttempt.disabled"  id="input-2" v-model="auth.password" name="password" :type="showPassword ? 'text' :  'password'"
                    placeholder="Enter password" autocomplete="off" ></b-form-input>
                </b-form-group>
                <b-form-checkbox  id="customControlInline" name="checkbox-1" v-model="showPassword" >
                  Show Password 
                </b-form-checkbox>
                <!-- <b-form-checkbox id="customControlInline" name="checkbox-1" value="accepted"
                  unchecked-value="not_accepted">
                  Remember me
                </b-form-checkbox> -->
                <div class="mt-3 d-grid">
                  <button type="submit" :disabled="processing || loginAttempt.disabled" class="mt-3 btn btn-primary btn-block">
                    {{ processing ? "Please wait" : "Login" }}
                  </button>
                </div>

                <div v-if="!loginAttempt.disabled" class="mt-4 text-center">
                  <router-link to="/forget-password" class="text-muted">
                    <i class="mdi mdi-lock mr-1"></i> Forgot your password?
                  </router-link>
                </div>
              </b-form>
            </div>
            <!-- end card-body -->
          </div>
          <!-- end card -->

          <!-- <form @submit.prevent="forgotPassword">
            <input type="text" v-model="forgotForm.email">
            <button type="submit">Submit</button>
          </form> -->

          <!-- <div class="mt-5 text-center">
           
            <p>
              © {{ new Date().getFullYear() }} Pharmims.
            </p>
          </div> -->
          <!-- end row -->
        </div>
        <!-- end col -->
      </div>
      <!-- end row -->
    </div>
  </div>
</template>

