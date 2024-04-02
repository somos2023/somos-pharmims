<script>
import Layout from "../../../layouts/main";
import PageHeader from "@/components/page-header";
import appConfig from "@/app.config";
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";

import { line, pie, Doughnut, radar, bar, PolarArea } from "./data";

/**
 * Chartjs-chart component
 */
export default {
  page: {
    title: "Chartjs Charts",
    meta: [{ name: "description", content: appConfig.description }],
  },
  components: {
    Layout,
    PageHeader,
    Vue3ChartJs,
  },
 
  data() {
    return {
      line: line,
      pie: pie,
      Doughnut: Doughnut,
      radar: radar,
      bar: bar,
      PolarArea: PolarArea,
      title: "Chartjs Charts",
      items: [
        {
          text: "Charts",
          href: "/",
        },
        {
          text: "Chartjs Charts",
          active: true,
        },
      ],
    };
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
            <div class="row text-center">
              <div class="col-sm-4">
                <h5 class="mb-0">86541</h5>
                <p class="text-muted">Activated</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">2541</h5>
                <p class="text-muted">Pending</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">102030</h5>
                <p class="text-muted">Deactivated</p>
              </div>
            </div>
            <!-- Line Chart -->
           
            <vue3-chart-js
              class="chartjs-chart"
              v-bind="{ ...line }"
            ></vue3-chart-js>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Bar Chart</h4>
            <div class="row text-center">
              <div class="col-sm-4">
                <h5 class="mb-0">2541</h5>
                <p class="text-muted">Activated</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">84845</h5>
                <p class="text-muted">Pending</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">12001</h5>
                <p class="text-muted">Deactivated</p>
              </div>
            </div>
           
            <!-- Bar Chart -->
            <vue3-chart-js
               class="chartjs-chart"
              v-bind="{ ...bar }"
            ></vue3-chart-js>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Pie Chart</h4>
            <div class="row text-center">
              <div class="col-sm-4">
                <h5 class="mb-0">2536</h5>
                <p class="text-muted">Activated</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">69421</h5>
                <p class="text-muted">Pending</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">89854</h5>
                <p class="text-muted">Deactivated</p>
              </div>
            </div>
            <!-- Pie Chart -->
             <vue3-chart-js
              :height="140"
              class="chartjs-chart"
              v-bind="{ ...pie }"
            ></vue3-chart-js>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Donut Chart</h4>
            <div class="row text-center">
              <div class="col-sm-4">
                <h5 class="mb-0">9595</h5>
                <p class="text-muted">Activated</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">36524</h5>
                <p class="text-muted">Pending</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">62541</h5>
                <p class="text-muted">Deactivated</p>
              </div>
            </div>
            <!-- Donut Chart -->
            <vue3-chart-js
              :height="140"
              class="chartjs-chart"
              v-bind="{ ...Doughnut }"
            ></vue3-chart-js>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Polar area Chart</h4>
            <div class="row text-center">
              <div class="col-sm-4">
                <h5 class="mb-0">4852</h5>
                <p class="text-muted">Activated</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">3652</h5>
                <p class="text-muted">Pending</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">85412</h5>
                <p class="text-muted">Deactivated</p>
              </div>
            </div>
            <!-- Polar area Chart -->
            <vue3-chart-js
              :height="140"
              class="chartjs-chart"
              v-bind="{ ...PolarArea }"
            ></vue3-chart-js>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Radar Chart</h4>
            <div class="row text-center">
              <div class="col-sm-4">
                <h5 class="mb-0">694</h5>
                <p class="text-muted">Activated</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">55210</h5>
                <p class="text-muted">Pending</p>
              </div>
              <div class="col-sm-4">
                <h5 class="mb-0">489498</h5>
                <p class="text-muted">Deactivated</p>
              </div>
            </div>
            <!-- Radar Chart -->
             <vue3-chart-js
              :height="140"
              class="chartjs-chart"
              v-bind="{ ...radar }"
            ></vue3-chart-js>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>