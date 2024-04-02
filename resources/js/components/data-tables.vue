<!-- <script setup>

	import { ref, computed, watchEffect, onMounted } from 'vue';
	import DataTable from 'datatables.net-vue3';
	import DataTablesCore from 'datatables.net';
	 
	DataTable.use(DataTablesCore);
	 
	const columns = [
	  { data: 'name', title: 'Name' },
	  { data: 'position', title: 'Position' },
	  { data: 'office', title: 'Office' },
	  { data: 'extn', title: 'Extension' },
	  { data: 'start_date', title: 'Start date' },
	  { data: 'salary', title: 'Salary' },
	];
</script>
 
<template>
    <DataTable
        :columns="columns"
        ajax="/data.json"
        class="display"
    />
</template>

<style>
	@import 'datatables.net-dt';
</style>
 -->