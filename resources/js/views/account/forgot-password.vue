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
            resend: false,
            email: "",
            error: "",
            status: "",
            isResetError: false,
            tryingToReset: false,
            processing: false,
            profileImg, logo
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
        this.tryingToReset = !!this.status;
    },
    methods: {
        async forget() {
            this.processing = true
            await axios.post('/api/forget-password', 
            {
                email:this.email, 
                resend: this.resend , 
            }
            )
            .then(({ data }) => {
                if (data.success == true && data.message == 'success') {
                    this.status = "Password reset link send in your email.";
                    this.tryingToReset = true;
                    this.isResetError = false;
                    this.resend = false;
                } else {
                    if (data.data == 400) {
                        this.error = data.message;
                        this.isResetError = true;
                    }
                    if(data.message == 'Reset link already sent'){
                        this.resend = true;
                    } else {
                        this.resend = false;
                    }
                }
            }).catch(({ response: { data } }) => {
                this.error = data.errors.email[0];
                this.isResetError = true;
                this.tryingToReset = false;
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
                                        <h5 class="text-primary">Forgot Password?</h5>
                                        <p>Enter your email to receive reset link</p>
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
                                <b-alert v-model="isResetError" class="mb-4" variant="danger" dismissible>{{ error
                                }}</b-alert>
                                <b-alert v-model="tryingToReset" class="mb-4" variant="success" dismissible>{{ status
                                }}</b-alert>
                                <form action="javascript:void(0)" method="POST">
                                    <slot />
                                    <div class="mb-3">
                                        <label for="useremail">Email</label>
                                        <input type="email" name="email" v-model="email" class="form-control" id="useremail"
                                            placeholder="Enter email" autocomplete="off"/>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-12 text-end">
                                            <b-button v-if="resend" type="submit" :disabled="processing" @click="forget()"
                                                variant="primary" class="btn-block">
                                                {{ processing ? "Please wait" : "Resend Link" }}
                                            </b-button>
                                            <b-button v-else type="submit" :disabled="processing" @click="forget()"
                                                variant="primary" class="btn-block">
                                                {{ processing ? "Please wait" : "Forget Password" }}
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
