<script setup>
import simplebar from "simplebar-vue";
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
// import { chatData, chatMessagesData } from "./data";
import avatar1 from '../../../images/users/avatar-1.jpg';

import { ref, computed, watchEffect, watch, onMounted} from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()


const user = computed(()=> store.state.user.data);
const listData = computed(()=> store.state.chatList);

const groupedMessages = ref([]);
const submitted = ref(false);
const form = ref({ message: "" });
const username = ref("");
let receiver_status = ref(false);
const chatForm = ref(true);

let count = ref(0);

const openChat = (contact)=> {
  username.value = contact.name;
  store.commit("setCurrentReceiver", contact.id);
  updateChat(contact.id);
}

const messageForm = () => {
  form.value.receiver = listData.value.data.receiver.id
  store.dispatch("saveChat", form.value)
  .then((res)=> {
    form.value = {}; 
    updateChat(listData.value.data.receiver.id);
    handleScroll();
  })
}

const deleteMessage = (id) => {
  console.log(id+'deleted')
  // groupedMessages.value.filter(item.id != id);
}

const current = ref(null)
const handleScroll = () => {
  if (current.value) {
    setTimeout(() => {
      const Element = document.querySelector("#containerElement");
      const containerElement = document.getElementById('containerElement');
      if (Element && containerElement) {
        const scrollHeight = containerElement.scrollHeight;
        Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
      }
    }, 500);
  }
};

const updateChat = (receiver)=> {
  store.dispatch('getChatData', receiver)
  .then(()=> {
    chatForm.value = listData.value.data.receiver ? false : true;
    username.value = listData.value.data.receiver ? listData.value.data.receiver.name : 'Chat Box' ;
    receiver_status.value = listData.value.data.receiver ? listData.value.data.receiver.online : false ;
    if(listData.value.data.message){
      groupedMessages.value = computed(() => {
        const grouped = {};
        listData.value.data.message.forEach((message) => {
          const day = message.label; // Assuming the label field indicates the day
          if (!grouped[day]) {
            grouped[day] = [];
          }
          grouped[day].push(message);
        });
        return grouped;
      });
    }
    handleScroll();
  });
}

onMounted(()=> {
  // console.log(sessionStorage.getItem("RECEIVER"));
  // setInterval(function(){
  //   updateChat(sessionStorage.getItem("RECEIVER") ?? 0);
  // }, 2000);
  updateChat(sessionStorage.getItem("RECEIVER") ?? 0);
})

</script>

<template>
  <Layout>
    <!-- <PageHeader :title="title" :items="items" /> -->
    <div class="d-lg-flex">
      <div class="chat-leftsidebar me-lg-4">
        <div class=""> 
          <div class="py-4 border-bottom">
            <div class="d-flex">
              <div class="align-self-center me-3">
                <img :src="user.image" class="avatar-xs rounded-circle" alt="" />
              </div>
              <div class="flex-grow-1">
                <h5 class="font-size-15 mt-0 mb-1">{{user.full_name}}</h5>
                <p class="text-muted mb-0">
                  <i class="mdi mdi-circle text-success align-middle me-1"></i>
                  Active
                </p>
              </div> 
            </div>
          </div>
          <div class="chat-leftsidebar-nav">
            <b-tabs pills fill content-class="py-4">
              <b-tab title="Tab 1" active>
                <template v-slot:title>
                  <i class="bx bx-chat font-size-20 d-sm-none"></i>
                  <span class="d-none d-sm-block">Chat</span>
                </template>
                <b-card-text>
                  <div>
                    <h5 class="font-size-14 mb-3">Recent</h5> 
                    <simplebar style="max-height: 410px" id="my-element">
                      <ul class="list-unstyled chat-list">
                        <li v-for="data of listData.data.recent" :key="data.id" @click="openChat(data)"
                          :class="{ active: username == data.name }">
                          <a href="javascript: void(0);">
                            <div class="d-flex">
                              <div class="align-self-center me-3">
                                <i :class="`mdi mdi-circle text-${data.color} font-size-10`"></i>
                              </div>
                              <div class="align-self-center me-3" v-if="data.image">
                                <img :src="`${data.image}`" class="rounded-circle avatar-xs" alt />
                              </div>
                              <div class="avatar-xs align-self-center me-3" v-if="!data.image">
                                <span class="
                                    avatar-title
                                    rounded-circle
                                    bg-soft bg-primary
                                    text-primary
                                  ">{{ data.name.charAt(0) }}</span>
                              </div>
                              <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-truncate font-size-14 mb-1">
                                  {{ data.name }}
                                </h5>
                                <p class="text-truncate mb-0">
                                  {{ data.message }}
                                </p>
                              </div>
                              <div class="font-size-11">{{ data.time }}</div>
                            </div>
                          </a>
                        </li>
                      </ul>
                    </simplebar>

                  </div>
                </b-card-text>
              </b-tab>
              <b-tab>
                <template v-slot:title>
                  <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                  <span class="d-none d-sm-block">Contacts</span>
                </template>
                <b-card-text>
                  <h5 class="font-size-14 mb-3">Contacts</h5>
                  <simplebar style="height: 410px">
                    <div v-for="(group, letter) in listData.data.contacts" :key="letter">
                      <div class="avatar-xs mb-3">
                        <span class="avatar-title rounded-circle bg-soft bg-primary text-primary">{{ letter }}</span>
                      </div>

                      <ul class="list-unstyled chat-list">
                        <li v-for="contact in group" :key="contact.id" @click="openChat(contact)" class="mb-3" :class="{ active: username == contact.name }">
                          <a href="#"  >
                            <h5 class="font-size-14 mb-0">{{ contact.first_name }} {{ contact.last_name }}</h5>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </simplebar>
                </b-card-text>
              </b-tab>
            </b-tabs>
          </div>
        </div>
      </div>  
      
      <div class="w-100 user-chat">
        <div v-if="groupedMessages"  class="card">
          <div class="p-4 border-bottom">
            <div class="row"> 
              <div class="col-md-4 col-9">
                <h5 class="font-size-15 mb-1"> {{ username }}</h5>
                <p v-if="receiver_status" class="text-muted mb-0">
                  <i class="mdi mdi-circle text-success align-middle me-1"></i>
                  Active now
                </p>
                <p v-else-if="receiver_status == false" class="text-muted mb-0">
                  <i class="mdi mdi-circle text-grey align-middle me-1"></i>
                  Offline
                </p>

              </div>
            </div>
          </div>
          <div class="chat-conversation p-3">
              <div style="max-height: 470px;min-height: 470px;" class="overflow-auto simplebar-content-wrapper" id="containerElement" ref="current">
                <ul  class="list-unstyled">
                  
                  <li v-for="(group, day) in groupedMessages.value" :key="day">
                    <div class="chat-day-title">
                      <span class="title">{{ day }}</span>
                    </div>
                    <li v-for="data in group" :key="data.id" :class="{ right: data.position === 'right' }">
                      <div class="conversation-list">
                        <!-- Your message display structure -->
                        <div class="ctext-wrap">
                          <div class="conversation-name">{{ data.first_name }}</div>
                          <p>{{ data.message }}</p>
                          <p class="chat-time mb-0">
                            <i class="bx bx-time-five align-middle me-1"></i>
                            {{ data.time }}
                          </p>
                        </div>
                      </div>
                    </li>
                  </li>
                </ul>
              </div>
            </div>
          <div class="chat-users">
            
            <div class="p-3 chat-input-section">
              <form @submit.prevent="messageForm" class="row">
                <div class="col">
                  <div class="position-relative">
                    <input autofocus :disabled="chatForm" type="text" v-model="form.message" class="form-control chat-input rounded"
                      placeholder="Enter Message..." />
                  </div>
                </div>
                <div class="col-auto">
                  <button :disabled="chatForm" type="submit" class="btn btn-primary btn-rounded chat-send w-md">
                    <span class="d-none d-sm-inline-block me-2">Send</span>
                    <i class="mdi mdi-send"></i>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
