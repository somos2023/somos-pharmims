<script>

import logoDark from '../../../images/logo-dark.png';
import logoLight from '../../../images/logo-light.png'
import maintenence from '../../../images/landing-image-1.svg'

export default {
  data() {
    return {
      logoDark, logoLight, maintenence
    }
  }
};
</script>
<template>
  <div>
    <!-- <div class="home-btn d-none d-sm-block">
      <router-link to="/dashboard" class="text-info">
        <i class="fas fa-user h2"></i>
      </router-link>
    </div> -->
    <section class="my-5 pt-sm-5">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <div class="home-wrapper">
              <!-- <div class="mb-5">
                <router-link to="/" class="d-block auth-logo">
                  <img
                    :src="logoDark"
                    alt=""
                    height="20"
                    class="auth-logo-dark mx-auto"
                  />
                  <img
                    :src="logoLight"
                    alt=""
                    height="20"
                    class="auth-logo-light mx-auto"
                  />
                </router-link>
              </div> -->
              <div class="mb-5">
                <h1 class="mt-5 text-uppercase fw-bolder">Holy Infant Savior Somos</h1>
                <p class="font-size-18">Multi-Specialty and Diagnostic Clinic</p>
              </div>
              <div class="row justify-content-center mb-5">
                <div class="col-sm-6">
                  <div class="maintenance-img">
                    <img
                      :src="maintenence"
                      alt
                      class="img-fluid mx-auto d-block"
                    />
                  </div>
                </div>
              </div>

              <router-link to="/user-selection" class="btn btn-primary">Get Started</router-link>
            
              <!-- end row -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>