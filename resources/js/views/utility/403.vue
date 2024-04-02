<script>
import errorImg from '../../../images/403 Error Forbidden-bro (1).svg';

export default {
  data() {
    return {
      errorImg
    }
  }
}
</script>
<template>
  <div class="account-pages my-5 pt-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="text-center mb-1">
            <h1 class="display-2 fw-medium">
              4<i class="bx bx-buoy bx-spin text-primary display-3"></i>3
            </h1>
            <h4 class="text-uppercase">Error, FORBIDDEN</h4>
            <div class="mt-5 text-center">
              <router-link class="btn btn-primary" to="/dashboard">Back to Dashboard</router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-8 col-xl-6">
          <div>
            <img :src="errorImg" alt class="img-fluid" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

