<script setup>
import NavBar from "../components/nav-bar.vue";
import SideBar from "../components/side-bar.vue";
import RightBar from "../components/right-bar.vue";
import Footer from "../components/footer.vue";
import { ref, onMounted, watchEffect, computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

const isMenuCondensed = ref(false);

const toggleMenu = () => {
    document.body.classList.toggle("sidebar-enable");

    if (window.screen.width >= 992) {
        document.body.classList.toggle("vertical-collpsed");
    } else {
        document.body.classList.remove("vertical-collpsed");
    }
    isMenuCondensed.value = !isMenuCondensed.value;
};

const toggleRightSidebar = () => {
    document.body.classList.toggle("right-bar-enabled");
};

const hideRightSidebar = () => {
    document.body.classList.remove("right-bar-enabled");
};

// Execute created logic (Note: Composition API doesn't have lifecycle hooks, this runs directly)
document.body.removeAttribute("data-layout", "horizontal");
document.body.removeAttribute("data-topbar", "dark");



const notify = computed(()=> store.state.notify);
const close = ()=> {
    notify.value.show = false;
}
</script>


<template>
    <div>
        <div id="layout-wrapper">
            <NavBar @toggle-menu="toggleMenu" @toggle-right-sidebar="toggleRightSidebar" />
            <SideBar :is-condensed="isMenuCondensed" />
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="main-content">
                <div class="page-content d-flex flex-column position-relative">
                    <!-- Start Content-->
                    <div class="container-fluid flex-grow-1">
                        <slot />
                    </div>
         

                    <div v-if="notify.show" style="z-index: 9999; max-width: 400px;" class="alert alert-dismissible alert-label-icon label-arrow fade position-fixed bottom-0 m-3  end-0" :class="notify.class">
                       
                            <i class="mdi label-icon" :class="notify.icon"></i> <strong>{{notify.title}}</strong>
                            - {{notify.message}}
                
                        <button type="button" class="btn-close" @click="close"></button>
                    </div>
                </div>
                <Footer />
            </div>
           


            <RightBar @hide-right-sidebar="hideRightSidebar"  />

        </div>
    </div>
</template>
