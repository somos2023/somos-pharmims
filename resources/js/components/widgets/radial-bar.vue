<script setup>
import { ref, onMounted, watchEffect } from "vue"

const props = defineProps({
   seriesData: {
      type: Number,
      default: 0
    },
    labelsData: {
      type: String,
      default: ''
    },
    labelString: {
      type: String,
      default: ''
    },

});

const chartOptions = ref({
        chart: {
          // offsetY: -10,
        },
        plotOptions: {
          radialBar: {
            startAngle: -135,
            endAngle: 135,
            dataLabels: {
              name: {
                fontSize: "13px",
                color: undefined,
                offsetY: 48
              },
              value: {
                offsetY: 15,
                fontSize: "14px",
                color: undefined,
                formatter: function(val) {
                  return val + props.labelString;
                }
              }
            }
          }
        },
        // colors: ["red"],
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.5,
            colorStops: [
              {
                offset: 20,
                color: "green",
                opacity: 1
              },
              {
                offset: 50,
                color: "yellow",
                opacity: 1
              },
              {
                offset: 100,
                color: "red",
                opacity: 1
              }
            ]
          },
        },
        stroke: {
          dashArray: 3
        },
        labels: [props.labelsData]
      });

const series = ref([]);

watchEffect(()=> {
  series.value = [props.seriesData];
})
</script>

<template>
  <div class="border">
  <apexchart

    class="apex-charts"
    type="radialBar"
    height="185"
    dir="ltr"
    :series="series"
    :options="chartOptions"
  ></apexchart>
  </div>
</template>
