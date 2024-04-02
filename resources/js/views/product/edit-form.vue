<script setup>
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
import no_image from "../../../images/no_image.png";

import { ref, computed, watchEffect, watch, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

const userData = computed(()=> store.state.user.data);

const items = ref([
        {
          text: "Dashboard",
          href: "/"
        },
    ]);

const form = ref({});
let submitted = ref(false);
const errors = ref({});
let update = ref(false);
let inAvailableStocks = ref(0);


const addStock = () => {
    let qty = form.value.add_stock;
    form.value.stock_added = qty;

    const avStock = form.value.available_stocks;
    if(qty > avStock){
        form.value.stock_added = avStock;
    } else if(qty < 0){
        form.value.stock_added = 0;   
    }
    form.value.stock = form.value.stock_added + form.value.old_stock;
    inAvailableStocks.value = avStock - form.value.stock_added;
}

const inputAdd = (qty, form) => {
    const avStock = form.available_stocks;
    if(qty > avStock){
        form.add_stock = avStock;
    } else if(qty < 0){
        form.add_stock = 0;
    }
}

watch(
  () => store.state.currentProduct,
  (newVal, oldVal) => {
    form.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: !!newVal.status,
    };
    form.value.expiration_date = form.value.expires_at;
    inAvailableStocks.value = form.value.available_stocks;
    form.value.old_stock = form.value.stock;
    form.value.stock_added = 0;
    form.value.add_stock = 0;
  }
);

function resetForm(){
    form.value = {...store.state.currentProduct} ;
    form.value.expiration_date = form.value.expires_at;
    inAvailableStocks.value = form.value.available_stocks;
    form.value.old_stock = form.value.stock;
    form.value.stock_added = 0;
    form.value.add_stock = 0;
}

if (route.params.id) {
    update.value = true;
    store.dispatch("getCurrentProduct", route.params.id)
}

const mainForm = ()=> {
    submitted.value = true;
    store.dispatch('saveProduct', form.value)
     .then((res) => {
        store.commit("notify", {
          show: true,
          title: 'Success',
          icon: 'mdi-check-all',
          classV: 'alert-success show',
          message: res.data.message,
        });
        submitted.value = false;
        errors.value = {};

        if(!update.value){
            form.value = {};
        } else {
            router.push({name: "product-list"})
        }
        
      })
      .catch(err => {
        errors.value = err.response.data.errors;
      });
}

const fileInput = ref(null);

const openFileInput = () => {
    fileInput.value.click();
  };
  

const handleFileInputChange = (ev) => {
  const file = ev.target.files[0];
  const reader = new FileReader();
  reader.onload = () => {
    form.value.image_url = reader.result;

    form.value.image = reader.result;
    ev.target.value = "";
  };
  reader.readAsDataURL(file);
}


onMounted(()=> {
    items.value.push({
      text: route.meta.title,
      active: true
    });
})


const barcode_scan = ref('')

const addBarcode = ()=> {
    if(barcode_scan.value !== ''){
        form.value.barcode = barcode_scan.value
        barcode_scan.value = ''
    }
    
}
</script>

<style>
  .avatar-container {
    position: relative;
    display: inline-block;
  }

  .edit-icon {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: white;
    border-radius: 50%;
    padding: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

</style>

<template>
  <Layout>
    <PageHeader :title="route.meta.title" :items="items" />
    <div v-if="userData.role_id == 3" class="row d-flex justify-content-center">
        <div vclass="col-lg-9 ">
            <div class="card">  
                <div class="card-body">
                    <h4 class="card-title mb-3 d-flex justify-content-between">Product Details <button @click="resetForm" class="cursor-pointer btn p-0 px-2 text-primary"><i class='bx bx-reset' ></i> Reset</button></h4> 

                    <form class="needs-validation" @submit.prevent="mainForm">
                        <div class="row mt-5 mb-3 ">
                            <div class="col-md-12 d-flex justify-content-center">
                                <div class="avatar-lg profile-user-wid mb-4">
                                   <div class="avatar-container position-relative">
                                      <img
                                        :src="form.image ? form.image : no_image"
                                        alt="profile image"
                                        class="img-thumbnail rounded-20 cursor-pointer"
                                        @click="openFileInput"
                                        type="button"
                                      />
                                      <input
                                        type="file"
                                        ref="fileInput"
                                        style="display: none"
                                        @change="handleFileInputChange"
                                      />
                                      <i class="fas fa-pencil-alt edit-icon" type="button" @click="openFileInput"></i>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row mb-5">
                            <form ref="addForm" @submit.prevent="addBarcode">
                                <div class="row d-flex justify-content-between mt-3">
                                  <div class="col-md-12">
                                      <div class="mb-3">
                                          <label for="barcode_scan"
                                            >Enter/Scan</label
                                          >
                                          <div class="d-flex col text-center">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="60" height="38" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M5 7h2v10H5zm9 0h1v10h-1zm-4 0h3v10h-3zM8 7h1v10H8zm8 0h3v10h-3z"></path><path d="M4 5h4V3H4c-1.103 0-2 .897-2 2v4h2V5zm0 16h4v-2H4v-4H2v4c0 1.103.897 2 2 2zM20 3h-4v2h4v4h2V5c0-1.103-.897-2-2-2zm0 16h-4v2h4c1.103 0 2-.897 2-2v-4h-2v4z"></path></svg>&nbsp;
                                          <input id="barcode_scan" autofocus="true" v-model="barcode_scan" type="text" class="form-control" placeholder="Barcode" ref="barcodeInput" />
                                          </div>
                                      </div>
                                  </div>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3 ">
                                    <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                    <input id="barcode" v-model="form.barcode" type="text" class="form-control " disabled placeholder="Enter barcode" :class="{ 'is-invalid': submitted && errors.barcode }" />
                                    <div v-if="submitted && errors.barcode" class="invalid-feedback">
                                        <span>{{errors.barcode[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="brand_name">Brand Name <span class="text-danger">*</span></label>
                                    <input id="brand_name" v-model="form.brand_name" type="text" class="form-control" placeholder="Brand name"  :class="{ 'is-invalid': submitted && errors.brand_name }" />
                                    <div v-if="submitted && errors.brand_name" class="invalid-feedback">
                                        <span>{{errors.brand_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="generic_name">Generic name</label>
                                    <input id="generic_name" v-model="form.generic_name" type="text" class="form-control" placeholder="Generic name" :class="{ 'is-invalid': submitted && errors.generic_name }" />
                                    <div v-if="submitted && errors.generic_name" class="invalid-feedback">
                                        <span>{{errors.generic_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dosage">Dosage Form <span class="text-danger">*</span></label>
                                    <input id="dosage" v-model="form.dosage" type="text" class="form-control" placeholder="Enter dosage" :class="{ 'is-invalid': submitted && errors.dosage }" />
                                    <div v-if="submitted && errors.dosage" class="invalid-feedback">
                                        <span>{{errors.dosage[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit">Unit of Measure <span class="text-danger">*</span></label>
                                    <input id="unit" v-model="form.unit" type="text" class="form-control" placeholder="Enter unit" :class="{ 'is-invalid': submitted && errors.unit }" />
                                    <div v-if="submitted && errors.unit" class="invalid-feedback">
                                        <span>{{errors.unit[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <input id="price" v-model="form.price" type="text" class="form-control" placeholder="Enter price" :class="{ 'is-invalid': submitted && errors.price }" />
                                    <div v-if="submitted && errors.price" class="invalid-feedback">
                                        <span>{{errors.price[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock">Stock</label>
                                    <input id="stock" 
                                    v-model="form.stock" type="number" min="0" class="form-control " placeholder="Enter stock" :class="{ 'is-invalid': submitted && errors.stock }" />
                                    <div v-if="submitted && errors.stock" class="invalid-feedback">
                                        <span>{{errors.stock[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="expiration_date">Expiration Date <span class="text-danger">*</span></label>
                                    <input id="expiration_date" v-model="form.expiration_date" type="date" class="form-control" placeholder="Enter expiration_date" :class="{ 'is-invalid': submitted && errors.expiration_date }" />
                                    <div v-if="submitted && errors.expiration_date" class="invalid-feedback">
                                        <span>{{errors.expiration_date[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Description </label>
                                    <textarea id="description" v-model="form.description" class="form-control" placeholder="Enter description" :class="{ 'is-invalid': submitted && errors.description }" ></textarea>
                                    <div v-if="submitted && errors.description" class="invalid-feedback">
                                        <span>{{errors.description[0]}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <router-link class="btn btn-danger mt-2 " :to="{name: 'product-list'}">Cancel</router-link> 
                            <button class="btn btn-primary mt-2 " type="submit">{{ update ? 'Update' : 'Submit'}}</button> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div v-if="userData.role_id == 1 || userData.role_id == 2" class="row d-flex justify-content-center">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4  class="card-title text-center mb-4">Available Stocks</h4>
                    <div class="text-center ">
                       <h3>{{inAvailableStocks}}</h3>
                    </div>
                    <div class="mt-3 mb-3">
                        <label for="stock">Add Stock </label>
                        <input id="stock" 
                        @input="inputAdd($event.target.value, form)"
                        :max="form.available_stocks"
                        v-model="form.add_stock"
                        type="number" min="0" class="form-control "  placeholder="Enter quantity"  />
                    </div>
                    <button @click="addStock" class="btn btn-primary mt-2" type="submit">Add</button> 
                </div>
            </div>
        </div>
        <div class="col-lg-9 ">
            <div class="card">  
                <div class="card-body">
                    <h4 class="card-title mb-3 d-flex justify-content-between">Product Details <button @click="resetForm" class="cursor-pointer btn p-0 px-2 text-primary"><i class='bx bx-reset' ></i> Reset</button></h4> 
                    <form class="needs-validation" @submit.prevent="mainForm">
                        <div class="row mt-5 mb-3 ">
                            <div class="col-md-12 d-flex justify-content-center">
                                <div class="avatar-lg profile-user-wid mb-4">
                                   <div class="avatar-container position-relative">
                                      <img
                                        :src="form.image ? form.image : no_image"
                                        alt="profile image"
                                        class="img-thumbnail rounded-20 cursor-pointer"
                                        @click="openFileInput"
                                        type="button"
                                      />
                                      <input
                                        type="file"
                                        ref="fileInput"
                                        style="display: none"
                                        @change="handleFileInputChange"
                                      />
                                      <i class="fas fa-pencil-alt edit-icon" type="button" @click="openFileInput"></i>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="brand_name">Brand Name <span class="text-danger">*</span></label>
                                    <input id="brand_name" v-model="form.brand_name" type="text" class="form-control" placeholder="Brand name"  :class="{ 'is-invalid': submitted && errors.brand_name }" />
                                    <div v-if="submitted && errors.brand_name" class="invalid-feedback">
                                        <span>{{errors.brand_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="generic_name">Generic name</label>
                                    <input id="generic_name" v-model="form.generic_name" type="text" class="form-control" placeholder="Generic name" :class="{ 'is-invalid': submitted && errors.generic_name }" />
                                    <div v-if="submitted && errors.generic_name" class="invalid-feedback">
                                        <span>{{errors.generic_name[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                    <input id="barcode" v-model="form.barcode" type="text" class="form-control" placeholder="Enter barcode" :class="{ 'is-invalid': submitted && errors.barcode }" />
                                    <div v-if="submitted && errors.barcode" class="invalid-feedback">
                                        <span>{{errors.barcode[0]}}</span>
                                    </div>
                                </div>
                            </div> -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dosage">Dosage Form <span class="text-danger">*</span></label>
                                    <input id="dosage" v-model="form.dosage" type="text" class="form-control" placeholder="Enter dosage" :class="{ 'is-invalid': submitted && errors.dosage }" />
                                    <div v-if="submitted && errors.dosage" class="invalid-feedback">
                                        <span>{{errors.dosage[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit">Unit of Measure <span class="text-danger">*</span></label>
                                    <input id="unit" v-model="form.unit" type="text" class="form-control" placeholder="Enter unit" :class="{ 'is-invalid': submitted && errors.unit }" />
                                    <div v-if="submitted && errors.unit" class="invalid-feedback">
                                        <span>{{errors.unit[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <input id="price" v-model="form.price" type="text" class="form-control" placeholder="Enter price" :class="{ 'is-invalid': submitted && errors.price }" />
                                    <div v-if="submitted && errors.price" class="invalid-feedback">
                                        <span>{{errors.price[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock">Stock</label>
                                    <input id="stock" 
                                    @input="updateStock(Number($event.target.value), form)"
                                    :max="form.available_stocks"
                                    v-model="form.stock" type="number" min="0" class="form-control " disabled placeholder="Enter stock" :class="{ 'is-invalid': submitted && errors.stock }" />
                                    <div v-if="submitted && errors.stock" class="invalid-feedback">
                                        <span>{{errors.stock[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="expiration_date">Expiration Date <span class="text-danger">*</span></label>
                                    <input id="expiration_date" v-model="form.expiration_date" type="date" class="form-control" placeholder="Enter expiration_date" :class="{ 'is-invalid': submitted && errors.expiration_date }" />
                                    <div v-if="submitted && errors.expiration_date" class="invalid-feedback">
                                        <span>{{errors.expiration_date[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Description </label>
                                    <textarea id="description" v-model="form.description" class="form-control" placeholder="Enter description" :class="{ 'is-invalid': submitted && errors.description }" ></textarea>
                                    <div v-if="submitted && errors.description" class="invalid-feedback">
                                        <span>{{errors.description[0]}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <router-link class="btn btn-danger mt-2 " :to="{name: 'product-list'}">Cancel</router-link> 
                            <button class="btn btn-primary mt-2 " type="submit">{{ update ? 'Update' : 'Submit'}}</button> 
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    
  </Layout>
</template>