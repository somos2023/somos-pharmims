<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";

import { ref, computed, watchEffect, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

store.dispatch('getCart');
const loading = computed(()=> store.state.cartList.loading);
const cartList = computed(()=> store.state.cartList);
const systemSet = computed(()=> store.state.system);
const userData = computed(()=> store.state.user.data);

const shopping = ()=> {
    sessionStorage.removeItem("PLACEORDER");
    router.push({name: "supplier-product"});
}
// -- end

const items = ref([
  {
    text: "Dashboard",
    href: "/"
  },
]);

onMounted(()=> {
  items.value.push({
    text: route.meta.title,
    active: true
  });
})

</script>
<template>
    <Layout>
        <div class="row d-flex justify-content-center mt-5">
            <div class="col-lg-5 ">
                <div class="card tex">
                    <div class="card-body">
                         <h4 class="mb-5 mt-2 text-center">
                            Place order success!
                         </h4>
                         <div class="col-sm-12 d-flex justify-content-between">
                           <a @click="shopping" class="btn btn-secondary">
                             <i class="mdi mdi-arrow-left me-1"></i> Continue Shopping
                           </a>
                           <router-link :to="{name: 'my-order'}" class="btn btn-primary">
                              View Order
                           </router-link>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

