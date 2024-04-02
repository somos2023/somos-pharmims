<script setup>
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import { ref, computed, watchEffect } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

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
  hideDelete: {
      type: Boolean,
      default: false,
    },
  hideActions: {
      type: Boolean,
      default: false,
    },
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

const deleteItem = (id, model) => {
  const newmodel = toUpperCaseFirst(model);
  if (window.confirm("Are you sure you want to delete this item?")) {
    store.dispatch(`delete${newmodel}`, id)
    .then((res) => {
        store.commit("notify", {
          show: true,
          title: 'Success',
          icon: 'mdi-check-all',
          classV: 'alert-success show',
          message: res.data.message,
        });
        emit("delete", id);
      })
      .catch(err => {
        store.commit("alert", {
          show: true,
          title: 'Error',
          icon: 'mdi-block-helper',
          classV: 'alert-danger show',
          message: err.response.data.message,
        });
      });
        
  }
};

function changePage(page) {
    emit("change", page)
}

const changeUserStatus = (id, status)=> {
  const data = {
      id: id,
      status: status
    };
  if(status){
    store.dispatch('changeUserStatus', data)
    .then((res) => {
        store.commit("notify", {
          show: true,
          title: 'Success',
          icon: 'mdi-check-all',
          classV: 'alert-success show',
          message: res.data.message,
        });
        props.items = props.items.filter(item => item.id !== id);
      })
      .catch(err => {
        store.commit("alert", {
          show: true,
          title: 'Error',
          icon: 'mdi-block-helper',
          classV: 'alert-danger show',
          message: err.response.data.message,
        });
      });
  }
}

const downloadTableData = (type) => {
  if(type === 'Excel'){
    downloadButton()
  } else {
    downloadButtonPDF()
  }
}

const downloadButton = () => {
  const headerRow = ['#', ...modifiedColumnData.value];
  const tableData = [headerRow, ...props.items.map((item, rowIndex) => {
    const rowData = [rowIndex + 1];
    props.columns.forEach((column, columnIndex) => {
      let value = item[column];
      
      // Check if the value is not a string, and convert it to a string if necessary
      if (typeof value !== 'string' && value !== null && value !== undefined) {
        value = String(value);
      }

      // Add quotes if the value contains a comma
      value = value.includes(',') ? `"${value}"` : value;

      rowData.push(value);
    });
    return rowData;
  })];

  const csvContent = tableData.map((row) => row.join(',')).join('\n');

  const currentDate = new Date().toISOString().slice(0, 10);
  const filename = `${props.model}_table_data_${currentDate}.csv`;

  const blob = new Blob([csvContent], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);

  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', filename);

  document.body.appendChild(link);
  link.click();

  document.body.removeChild(link);
}


const downloadButtonPDF = () => {
      const headerRow = ['#', ...modifiedColumnData.value];
      const tableData = [headerRow, ...props.items.map((item, rowIndex) => {
        const rowData = [rowIndex + 1];
        props.columns.forEach((column) => {
          rowData.push(item[column]);
        });
        return rowData;
      })];

      const currentDate = new Date().toISOString().slice(0, 10);
      const filename = `${props.model}_table_data_${currentDate}.pdf`;

      // Create a new jsPDF instance
      const pdf = new jsPDF();
      
      // Define the columns and rows
      const columns = headerRow;
      const rows = tableData;

      // Add content to PDF
      pdf.autoTable({
        head: [columns],
        body: rows,
      });

      // Save the PDF
      pdf.save(filename);
    };




</script>
<template> 
  <div v-if="columns.length != 0" class="card">
      <div class="card-body">
        <div class="btn-group gap-2 ">  
          <button class="mb-3 btn btn-primary btn-sm" @click="downloadTableData('Excel')">Download Excel</button>
          <button class="mb-3 btn btn-primary btn-sm" @click="downloadTableData('PDF')">Download PDF</button>
        </div>
        <div class="table-responsive">
          <table ref="dataTable" class="table table-hover table-bordered table-ls mb-0">
            <thead class="p-5">
                <th>#</th>
                <th v-for="(column, index) in modifiedColumnData" :key="index">{{ column }}</th>
            </thead>
            <tbody>
                <tr v-if="items.length > 0" v-for="(item, rowIndex) in items" :key="rowIndex">
                    <th scope="row">{{rowIndex+1}}</th>

                    <td v-for="(column, colIndex) in columns" :key="colIndex">
                        <div v-if="column == 'image'">
                            <img
                              class="rounded-circle header-profile-user"
                              :src="item[column]"
                              alt="Header Avatar"
                            />
                        </div>
                        
                        <div v-else>
                            {{ item[column] }}
                        </div>
                       
                    </td>
                </tr>
                <tr v-else>
                  <th scope="row">
                    No Records Found
                  </th>
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
      </div>
    </div>
</template>
