<script>
import { useStore } from "vuex";
import { useRouter, useRoute } from "vue-router";
import { ref, computed, onMounted, watchEffect} from "vue";
import profileImg from '../../../images/profile-img.png';
import logo from '../../../images/logo.svg';
import axios from 'axios';

/**
 * Forgot Password component
 */
export default {
    data() {
        return {
            email: "",
            password: "",
            password_confirmation: "",
            error: "",
            isResetError: false,
            profileImg,
            logo,
            processing: false,
            showPassword: false
        };
    },
    setup() {
        const store = useStore();

        // Fetch system info when the component is mounted
        onMounted(async () => {
            await store.dispatch('getSystemInfo');
        });

        const sysInfo = computed(() => store.state.system);

        return { store, sysInfo };
    },
    mounted() {
        this.isResetError = !!this.error;
    },
    methods: {
        async reset() {
            this.processing = true
            // this.
            await axios.post('/api/reset-password', 
            {
                email:this.email,
                token:this.$route.params.token,
                password: this.password,
                password_confirmation: this.password_confirmation
            }).then(({ data }) => {
                if (data.success == true && data.message == 'success') {
                    this.$router.push('/user-selection');
                } else {
                    if (data.data == 400) {
                        this.error = data.message;
                        this.isResetError = true;
                        this.processing = false
                    }
                }
            }).catch(({ response: { data } }) => {
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
                                        <h5 class="text-primary">Reset Password</h5>
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
                            <div class="p-2">
                                <b-alert v-model="isResetError" class="mb-4" variant="danger"
                                    dismissible>{{ error }}</b-alert>
                                <form @submit.prevent="reset" >
                                    <slot />
                                    <b-form-group id="email" label="email" :value="email" label-for="email">
                                        <b-form-input id="email" v-model="email" name="email" type="email"
                                            placeholder="Enter email" autocomplete="off" ></b-form-input>
                                    </b-form-group>
                                    <b-form-group id="password-group" label="Password" label-for="password">
                                        <b-form-input id="password" v-model="password" name="password" :type="showPassword ? 'text' :  'password'"
                                            placeholder="Enter password" autocomplete="off" ></b-form-input>
                                    </b-form-group>
                                    <b-form-group label="Confirm Password" label-for="password-confirm">
                                        <b-form-input id="password-confirm" v-model="password_confirmation" name="password_confirmation" :type="showPassword ? 'text' :  'password'"
                                            placeholder="Confirm password" autocomplete="off" ></b-form-input>
                                    </b-form-group>
                                    <b-form-checkbox  id="customControlInline" name="checkbox-1" v-model="showPassword" >
                                      Show Password 
                                    </b-form-checkbox>
                                    <div class="form-group row mb-0">
                                        <div class="col-12 text-end">
                                            <b-button type="submit" :disabled="processing"
                                                variant="primary" class="btn-block">
                                                {{ processing ? "Please wait" : "Reset Password" }}
                                            </b-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="mt-5 text-center">
                        <p>
                            Remember It ?
                            <router-link to="/user-selection" class="fw-medium text-primary">Sign In here</router-link>
                        </p>
                        <!-- <p>
                            © {{ new Date().getFullYear() }} Pharmims. 
                        </p> -->
                    </div>
                </div>
                <!-- end col -->
            </div>
        </div>
    </div>
</template>