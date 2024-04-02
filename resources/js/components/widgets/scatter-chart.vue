<script setup>
import { ref, onMounted, watchEffect } from "vue"

const props = defineProps({
   scatterData: {
      type: Object,
      default: {}
    },
  cardTitle: {
      type: String,
      default: ''
    },

});

const chartOptions = ref({
            chart: {
              height: 350,
              type: 'scatter',
              zoom: {
                enabled: true,
                type: 'xy'
              },
              toolbar: {
                show: true,
              },
            },
            xaxis: {
              tickAmount: 10,
                type: 'datetime', // If expires_at is a datetime field
                labels: {
                    datetimeFormatter: {
                        year: 'yyyy',
                        month: 'MMM \'yy',
                        day: 'dd HH:mm'
                    }
                }
            },
            yaxis: {
              tickAmount: 7,
              title: {
                        text: 'Quantity'
                    }
            },
            tooltip: {
              custom: function ({ seriesIndex, dataPointIndex, w }) {
                  const item = w.config.series[seriesIndex].data[dataPointIndex];
                  return `<div style="padding: 2px 3px">
                              <span>${item.brand_name}</span>: ${item.y} units
                              <br>Order #: ${item.order_number}
                              <br>Date: ${item.x}
                          </div>`;
              }
          },
          });

// const series = ref([]);

// watchEffect(()=> {
//    series.value[0].data = props.scatterData.x;
// })
</script>

<template>
  <div class="card">
      <div class="card-body">
        <div class="d-sm-flex flex-wrap">
          <h4 class="card-title mb-4">{{cardTitle}}</h4>
        </div>
        <div id="chart">
          <apexchart type="scatter" height="350" :options="chartOptions" :series="[{
            name: 'Expired Product',
            data: scatterData
        }]"></apexchart>
        </div>
    </div>
  </div>
</template>
