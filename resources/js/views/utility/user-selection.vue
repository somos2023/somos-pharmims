<script>

import logoDark from '../../../images/logo-dark.png';
import logoLight from '../../../images/logo-light.png'
import maintenence from '../../../images/user-selection-img-1.svg'

export default {
  data() {
    return {
      logoDark, logoLight, maintenence
    }
  }
};
</script>

<style>
  .my-custom-class {
     height: 32px; /* Adjust height */
    width: 28px; /* Adjust width */
    fill: #6DB9EF; /* For example, changes the fill color to blue */
}
</style>
<template>
  <div>
    <div class="home-btn d-none d-sm-block">
      <router-link :to="{name: 'home'}" class="text-dark">
        <i class="fas fa-home h2"></i>
      </router-link>
    </div>
    <section class="my-5 pt-sm-5">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <div class="home-wrapper">
              

              <div class="row justify-content-center">
                <div class="col-sm-4">
                  <div class="maintenance-img">
                    <img
                      :src="maintenence"
                      alt
                      class="img-fluid mx-auto d-block"
                    />
                  </div>
                </div>
              </div>
              <h2 class="mt-5 text-uppercase">Users</h2>

              <div class="row">
                <router-link to="/login/admin"  class="col-md-4">
                  <div class="card mt-4 maintenance-box">
                    <div class="card-body">
                     <svg  xmlns="http://www.w3.org/2000/svg" class="my-custom-class mb-3" height="16" width="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-96 55.2C54 332.9 0 401.3 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7c0-81-54-149.4-128-171.1V362c27.6 7.1 48 32.2 48 62v40c0 8.8-7.2 16-16 16H336c-8.8 0-16-7.2-16-16s7.2-16 16-16V424c0-17.7-14.3-32-32-32s-32 14.3-32 32v24c8.8 0 16 7.2 16 16s-7.2 16-16 16H256c-8.8 0-16-7.2-16-16V424c0-29.8 20.4-54.9 48-62V304.9c-6-.6-12.1-.9-18.3-.9H178.3c-6.2 0-12.3 .3-18.3 .9v65.4c23.1 6.9 40 28.3 40 53.7c0 30.9-25.1 56-56 56s-56-25.1-56-56c0-25.4 16.9-46.8 40-53.7V311.2zM144 448a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg>

                      <h5 class="font-size-15 text-uppercase">
                        Admin
                      </h5>
                    </div>
                  </div>
                </router-link>
                <router-link to="/login/staff"  class="col-md-4">
                  <div class="card mt-4 maintenance-box">
                    <div class="card-body">
                      <svg xmlns="http://www.w3.org/2000/svg" class="my-custom-class mb-3" height="16" width="12" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM128 256a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM80 432c0-44.2 35.8-80 80-80h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16z"/></svg>
                      <h5 class="font-size-15 text-uppercase">
                        Staff
                      </h5>
                      
                    </div>
                  </div>
                </router-link>
                <router-link to="/login/supplier"  class="col-md-4">
                  <div class="card mt-4 maintenance-box">
                    <div class="card-body">
                      <svg xmlns="http://www.w3.org/2000/svg" class="my-custom-class mb-3" height="16" width="20" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M32 96c0-35.3 28.7-64 64-64H320c23.7 0 44.4 12.9 55.4 32h51.8c25.3 0 48.2 14.9 58.5 38l52.8 118.8c.5 1.1 .9 2.1 1.3 3.2H544c35.3 0 64 28.7 64 64v32c17.7 0 32 14.3 32 32s-14.3 32-32 32H576c0 53-43 96-96 96s-96-43-96-96H256c0 53-43 96-96 96s-96-43-96-96H32c-17.7 0-32-14.3-32-32s14.3-32 32-32V288c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32V96zM384 224h85.9l-42.7-96H384v96zM160 432a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm368-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0z"/></svg>
                      <h5 class="font-size-15 text-uppercase">
                        Supplier
                      </h5>
                      
                    </div>
                  </div>
                </router-link>
              </div>
              <!-- end row -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>