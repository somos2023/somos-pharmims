<script setup>
  import NumberDynamic from "../number-dynamic.vue"

  const props = defineProps({
    number: {
      type: Number,
      default: 0
    },
    icon: {
      type: String,
      default: null
    },
    prefix: {
      type: String,
      default: null
    },
    suffix: {
      type: String,
      default: null
    },
    label: {
      type: String,
      default: null
    },
  })
</script>

<template>
  <div class="card mini-stats-wid">
    <div class="card-body">
      <div class="d-flex">
        <div class="flex-grow-1">
          <p class="text-muted fw-medium">{{label}}</p>
          <h4 class="mb-0">
            <NumberDynamic :value="number" :prefix="prefix" :suffix="suffix" />
          </h4>
        </div>

        <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-primary">
          <span class="avatar-title">
            <i :class="`${icon} font-size-24`"></i>
          </span>
        </div>
      </div>
    </div>
    <!-- end card-body -->
  </div>
  <!-- end card -->
</template>