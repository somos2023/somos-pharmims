<script setup>
import { ref, computed, watchEffect } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';
import RadialBar from "./radial-bar.vue";

const store = useStore();
const router = useRouter()
const route = useRoute()

const props = defineProps({
  items: Object,
  columns: Array,
  model: String,
  pages: Array,
  current: Number,
  human: Number,
  num_pages: Number,
});

const emit = defineEmits(["change", "delete"]);

const modifiedColumnData = computed(() => {
  return props.columns.map(column => {
    return column.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  });
});


const toUpperCaseFirst = (str)=> {
  return str.charAt(0).toUpperCase() + str.slice(1);
}


function changePage(page) {
    emit("change", page)
}

</script>
<template> 
  <div class="table-responsive">
    <table ref="dataTable" class="table table-hover table-bordered table-ls mb-0">
      <thead class="p-5">
          <th>#</th>
          <th class="text-center" v-for="(column, index) in modifiedColumnData" :key="index">{{ column }}</th>
      </thead>
      <tbody>
          <tr v-for="(item, rowIndex) in items" :key="rowIndex">
              <th scope="row">{{rowIndex+1}}</th>

              <td v-for="(column, colIndex) in columns" :key="colIndex">
                  <div v-if="column == 'image'">
                      <img
                        class="rounded-circle header-profile-user"
                        :src="item[column]"
                        alt="Header Avatar"
                      />
                  </div>
                  <!-- <div class="" v-else-if="(column == 'stock_percentage' || column == 'stock') && model == 'critical'">
                     <RadialBar :seriesData="item[column]" labelsData="Stock" />
                  </div> -->
                  <div v-else>
                      {{ item[column] }}
                  </div>
              </td>
          </tr>
      </tbody>
     
      </table>
      <div class="mt-4">

        <div class="btn-toolbar justify-content-between align-items-center" >
          <div class="btn-group me-2 " role="group"> 
              <b-button  @click="changePage(current-1)" :disabled="current == 0" variant="light">
                <i class="fa fa-chevron-left" ></i>
              </b-button>
              <b-button variant="light" v-for="page in pages" :key="page" @click="changePage(page)" type="button" :class="{'active': page === current}"  >
                {{page + 1}}
              </b-button>
              <b-button  @click="changePage(current+1)" :disabled="current == (num_pages-1)" variant="light">
                <i class="fa fa-chevron-right" ></i>
              </b-button>
          </div>
          <small>Page {{ human }} of {{ num_pages }}</small>
        </div>
      </div>
  </div>
</template>
