<script>
import { VueEcharts } from "vue3-echarts";
// import "echarts/lib/chart/line";
// import "echarts/lib/chart/bar";
// import "echarts/lib/chart/pie";
// import "echarts/lib/chart/scatter";
// import "echarts/lib/chart/candlestick";
// import "echarts/lib/chart/gauge";

// import "echarts/lib/component/legend";
// import "echarts/lib/component/title";
// import "echarts/lib/component/tooltip";
// import "echarts/lib/component/polar";
// import "echarts/lib/component/toolbox";
// import "echarts/lib/component/grid";
// import "echarts/lib/component/axis";

import Layout from "../../../layouts/main";
import PageHeader from "@/components/page-header";
import appConfig from "@/app.config";

import {
  polarChart,
  lineChart,
  pieChart,
  bubbleChart,
  doughnutChart,
  mixedBarChart,
  guageChart,
  candleStickChart,
} from "./data";

export default {
  page: {
    title: "Echart Chart",
    meta: [{ name: "description", content: appConfig.description }],
  },
  components: { Layout, PageHeader, VueEcharts },
  data() {
    return {
      // polarChart: polarChart,
      // lineChart: lineChart,
      // pieChart: pieChart,
      // bubbleChart: bubbleChart,
      // doughnutChart: doughnutChart,
      // mixedBarChart: mixedBarChart,
      // candleStickChart: candleStickChart,
      // guageChart: guageChart,
      title: "ECharts",
      items: [
        {
          text: "Charts",
          href: "/",
        },
        {
          text: "ECharts",
          active: true,
        },
      ],
    };
  },
  computed: {
    option() {
      return {
        polarChart: polarChart,
        lineChart: lineChart,
        pieChart: pieChart,
        bubbleChart: bubbleChart,
        doughnutChart: doughnutChart,
        mixedBarChart: mixedBarChart,
        candleStickChart: candleStickChart,
        guageChart: guageChart,
      };
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Line Chart</h4>
            <vue-echarts
              :option="option.lineChart"
              style="height: 300px"
              ref="chart"
            />
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Mix Line-Bar</h4>
            <vue-echarts
              :option="option.mixedBarChart"
              style="height: 300px"
              ref="chart"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Pie Chart</h4>
            <vue-echarts :option="option.pieChart" style="height: 300px" />
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Doughnut Chart</h4>
            <vue-echarts :option="option.doughnutChart" style="height: 300px" />
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Candle Stick Chart</h4>
            <vue-echarts
              :option="option.candleStickChart"
              style="height: 300px"
            />
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Gauge Chart</h4>
            <vue-echarts :option="option.guageChart" style="height: 300px" />
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Bubble Chart</h4>
            <vue-echarts :option="option.bubbleChart" style="height: 300px" />
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Polar Chart</h4>
            <vue-echarts
              :option="option.polarChart.polar"
              style="height: 300px"
            />
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>