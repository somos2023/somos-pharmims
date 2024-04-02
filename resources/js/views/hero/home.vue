<template >
  <!-- Mashead header-->
  <main style="position: relative;  background-color: #fff;">
    <b-carousel
      v-model="slide"
      ref="myCarousel"
      id="carousel-no-animation"
      fade
      controls
      indicators
      img-width="1024"
      img-height="960"

    >
      <b-carousel-slide img-src="http://127.0.0.1:8000/defaults/somos-img-1.jpg"></b-carousel-slide>
      <b-carousel-slide img-src="http://127.0.0.1:8000/defaults/somos-img-2.jpg"></b-carousel-slide>
      <b-carousel-slide img-src="http://127.0.0.1:8000/defaults/med-1.jpg"></b-carousel-slide>
    </b-carousel>
    <!-- h-100 mask text-light d-flex justify-content-center flex-column text-center" style="background-color: rgba(0, 0, 0, 0.4)" -->
    <div class="overlay-content w-75">
      <div class="container">
        <div class="mb-5 mb-lg-0 text-center text-lg-start text-white">
          <h1 class="display-1 fw-bold lh-1 mb-3 text-shadow" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">Holy Infant Savior Somos</h1>
          <p class="lead text-white mb-5" style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);">Multi-Specialty and Diagnostic Clinic</p>
          <div class=" p-5">
            <!-- <router-link to="/user-selection" class=" btn btn-light p-3 px-4">Get Started</router-link> -->
          </div>
           <div class=" p-5">
          </div>
        </div>
      </div>
    </div>
  </main>
  <section class="bg-light">
      <div class="container p-5">
          <div class="row gx-5 align-items-center justify-content-center justify-content-lg-between">
              <div class="col-12 col-lg-5">
                  <h2 class="display-4 lh-1 mb-4">{{info.name}}</h2>
                  <p class="lead fw-normal text-muted mb-5 mb-lg-0">{{info.title}}</p>
                
              </div>
              <div class="col-sm-8 col-md-6">
                  <div class="px-5 px-sm-0"><img class="img-fluid" src="../../../images/landing-image-1.svg" alt="..." /></div>
              </div>
          </div>
      </div>

  </section>
</template>

<script setup>
import { ref, onMounted, computed, watchEffect } from 'vue'

const myCarousel = ref(null)
const slide = ref(0)
const sliding = false

const {info} = defineProps({
  info: {
    type: Object,
    default: {}
  }
})
// function startAutoSlide() {
//   // Start auto slide after component is mounted
//   setInterval(() => { 
//     // if (!sliding) {
//     //   console.log(slide.value)
//     //   // Only change slide if not currently sliding
//     //   slide.value = (slide.value + 1) % 3;
//     // }
//     myCarousel.value.next()

//   }, 5000); // Change slide every 5 seconds (adjust as needed)
// }

// onMounted(()=> {
//   startAutoSlide()
// })
</script>


<style>
.overlay-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  text-align: center;
  z-index: 9999;
}
</style>