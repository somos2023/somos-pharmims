<script setup>
import { ref, onMounted, watchEffect } from "vue"

const props = defineProps({
  columnData: {
    type: Object,
    default: {}
  },
  cartTitle: {
    type: String,
    default: ''
  },

});

const series = ref([
    {
      name: '',
      data: [23,23,22,23,]
    }
  ]);  
const categories = ref([]);


let isActive = ref("year");
const chartOptions = ref({
        chart: {
          stacked: true,
          toolbar: {
            show: false,
          },
          zoom: {
            enabled: true,
          },
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "15%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        xaxis: {
          categories: categories,
        },
        colors: ["#556ee6", "#f1b44c", "#34c38f"],
        legend: {
          position: "bottom",
        },
        fill: {
          opacity: 1,
        },
      });



const changeVal = (value) => {
      switch (value) {
        case "month":
          isActive.value = "month";
          categories.value = props.columnData.month.categories;
          series.value[0].data = props.columnData.month.series;
          break;
        case "week":
          isActive.value = "week";
          categories.value = props.columnData.week.categories;
          series.value[0].data = props.columnData.week.series;
          break;
        case "year":
          isActive.value = "year";
          categories.value = props.columnData.year.categories;
          series.value[0].data = props.columnData.year.series;
          break;
        default:
          categories.value = props.columnData.year.categories;
          series.value[0].data = props.columnData.year.series;
          break;
      }
    };
    

watchEffect(()=> {
  series.value[0].data = props.columnData.year.series;

  categories.value = props.columnData.year.categories;
})
</script>

<template>
 <div class="card">
      <div class="card-body">
        <div class="d-sm-flex flex-wrap">
          <h4 class="card-title mb-4">{{cartTitle}}</h4>
          <div class="ms-auto">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a
                  class="nav-link"
                  href="javascript: void(0);"
                  @click="changeVal('week')"
                  :class="{'active': isActive == 'week'}"
                  >Week</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  href="javascript: void(0);"
                  @click="changeVal('month')"
                  :class="{'active': isActive == 'month'}"
                  >Month</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  href="javascript: void(0);"
                  @click="changeVal('year')"
                  :class="{'active': isActive == 'year'}"
                  >Year</a
                >
              </li>
            </ul>
          </div>
        </div>
        <div id="chart">
          <apexchart
            class="apex-charts"
            type="bar"
            dir="ltr"
            height="360"
            :series="series"
            :options="chartOptions"
          ></apexchart>
        </div>
      </div>
    </div>
</template>
