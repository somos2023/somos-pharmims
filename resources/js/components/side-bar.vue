<script>
import { useStore } from 'vuex'
import simplebar from "simplebar-vue";

import SideNav from "./side-nav.vue";
import StaffSideNav from "./staff-side-nav.vue";
import SupplierSideNav from "./supplier-side-nav.vue";

/**
 * Sidebar component
 */
export default {
  components: { simplebar, SideNav, StaffSideNav, SupplierSideNav },
  props: {
    isCondensed: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      store: useStore(),
      settings: {
        minScrollbarLength: 60,
      },
    };
  },
  methods: {
    onRoutechange() {
      setTimeout(() => {
        if(document.getElementsByClassName("mm-active").length > 0) {
        const currentPosition = document.getElementsByClassName("mm-active")[0].offsetTop;
        if (currentPosition > 500)
          this.$refs.currentMenu.SimpleBar.getScrollElement().scrollTo({ top: currentPosition + 300, behavior: "smooth" });;
        }
      }, 300);
    },
  },
  watch: {
    $route: {
      handler: "onRoutechange",
      immediate: true,
      deep: true,
    },
  }
};
</script>

<template>
  <!-- ========== Left Sidebar Start ========== -->
  <div class="vertical-menu">
    <simplebar
      v-if="!isCondensed"
      :settings="settings"
      class="h-100"
      ref="currentMenu"
       id="my-element"
    >
      <SideNav v-if="store.state.user.data.role_id == 1"  />
      <StaffSideNav v-if="store.state.user.data.role_id == 2"  />
      <SupplierSideNav v-if="store.state.user.data.role_id == 3"  />
    </simplebar>

    <simplebar v-else class="h-100">
      <SideNav v-if="store.state.user.data.role_id == 1"  />
      <StaffSideNav v-if="store.state.user.data.role_id == 2"  />
      <SupplierSideNav v-if="store.state.user.data.role_id == 3"  />
    </simplebar>
  </div>
  <!-- Left Sidebar End -->
</template>

