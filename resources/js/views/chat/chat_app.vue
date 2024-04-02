<script setup>
import simplebar from "simplebar-vue";
import Layout from "../../layouts/main.vue";
import PageHeader from "../../components/page-header.vue";
// import { chatData, chatMessagesData } from "./data";
// import avatar1 from '../../../images/users/avatar-1.jpg';

import { ref, computed, watchEffect, watch, onMounted, onUpdated } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

const store = useStore();
const router = useRouter()
const route = useRoute()

// store.commit('setChat', true)

const user = computed(()=> store.state.user.data);

const loading = computed(()=> store.state.chatData.loading);
const sending = computed(()=> store.state.sending);
const chatUsers = computed(()=> store.state.chatData.users);
const currentID = computed(()=> store.state.chatData.current);
const currentMessages = computed(()=> store.state.chatData.messages);

const recentChats = ref([]);
const groupedContacts = ref([]);
const currentUser = ref({});
const groupedMessages = ref([]);
const currentLength = ref(0);

const formData = ref({})
const chatContainer = ref(null);
const scrolling = ref(false);
const showScrollButton = ref(false);

const handleScroll = () => {
  if (chatContainer.value) {
    setTimeout(() => {
      const Element = document.querySelector("#containerElement");
      const containerElement = document.getElementById('containerElement');
      if (Element && containerElement) {
        const scrollHeight = containerElement.scrollHeight;
        Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
      }
      currentLength.value = currentMessages.value.length;

    }, 500);
  }
};

watchEffect(()=> {

  groupedContacts.value = computed(()=> {
    const contacts = {};

    // Sort users by name
    const sortedUsers = chatUsers.value.sort((a, b) =>
      a.name.localeCompare(b.name)
    );

    // Group users by first letter
    sortedUsers.forEach((user) => {
      const firstLetter = user.name.charAt(0).toUpperCase();
      if (!contacts[firstLetter]) {
        contacts[firstLetter] = [];
      }
      contacts[firstLetter].push(user);
    });

    return contacts;
  });

  currentUser.value = chatUsers.value.find(item => item.id == currentID.value)

  groupedMessages.value = computed(() => {
    const grouped = {};
    currentMessages.value.forEach((message) => {
      const day = message.label; // Assuming the label field indicates the day
      if (!grouped[day]) {
        grouped[day] = [];
      }
      grouped[day].push(message);
    });
    return grouped;
  });

  recentChats.value = chatUsers.value.filter(item => item.recent_message != null)
  
  if(currentMessages.value.length > currentLength.value){
    if(scrolling.value == false){
      handleScroll()
    }
  }

});


function openChat(id){ 
  store.commit('setCurrentReceiver', id)
  clearInterval(store.state.isChat);
  store.dispatch('getMessages', { receiver_id: id })
    .then(()=> {
      startUpdateChat()
      currentLength.value = currentMessages.value.length;
    })
  
  
}


function chatForm(){
  clearInterval(store.state.isChat);
  formData.value.receiver_id = currentID;
  if(formData.value.receiver_id){
    store.dispatch('saveMessage', formData.value)
      .then(()=> {
        formData.value = {}
        startUpdateChat()
      });
  }
  
}

function handleScrolling(){
  scrolling.value = true;
  const container = chatContainer.value;
  setTimeout(() => {
    if (scrolling.value) {
      scrolling.value = false;
    } 
  }, 5000);

  // const Element = document.querySelector("#containerElement");
  //     const containerElement = document.getElementById('containerElement');
  //     if (Element && containerElement) {
  //       const scrollHeight = containerElement.scrollHeight;
  //       // Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
  //     }

      // showScrollButton.value = true

  // showScrollButton.value = (chatContainer.value.scrollHeight - chatContainer.value.scrollTop) !==  chatContainer.value.scrollHeight 
}

// function scrollToBottom() {
//   const container = chatContainer.value;

//   chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
// }

function startUpdateChat(){
  store.state.isChat = setInterval(function () {
    if (!store.sending) {
      store.dispatch('getChatData')
      .catch(err=> {
        clearInterval(store.state.isChat);
      });
    }
  }, 4000);
}

onMounted(()=> {
  startUpdateChat()

  if(currentID.value != 0){
    openChat(currentID.value)
  } 

  

})

</script>

<template>
  <Layout> 
    <div class="text-center" v-if="currentUser && Object.keys(currentUser).length == 0">
      <span>loading...</span>
    </div>
    <div v-else class="d-lg-flex"> 
      <div class="chat-leftsidebar me-lg-4 ">
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
                        <li v-for="data of recentChats" :key="data.id" @click="openChat(data.id)"
                          :class="{ active: currentID == data.id }">
                          <a href="javascript: void(0);">
                            <div class="d-flex">
                              <div class="align-self-center me-3">
                                <i v-if="data.online_status" class="mdi mdi-circle text-success font-size-10"></i>
                                <i v-else class="mdi mdi-circle text-gray font-size-10"></i>
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
                                  ">{{ data.first_name.charAt(0) }}</span>
                              </div>
                              <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-truncate font-size-14 mb-1">
                                  {{ data.first_name }}
                                </h5>
                                <p class="text-truncate mb-0">
                                  {{data.recent_message.message}}  
                                </p>
                              </div>
                              <div class="font-size-11">{{ data.recent_message.time }}</div>
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
                    <div v-for="(group, letter) in groupedContacts.value" :key="letter">
                      <div class="avatar-xs mb-3">
                        <span class="avatar-title rounded-circle bg-soft bg-primary text-primary">{{ letter }}</span>
                      </div>

                      <ul class="list-unstyled chat-list">
                        <li v-for="contact in group" :key="contact.id" @click="openChat(contact.id)" class="mb-3" :class="{ active: currentID == contact.id }">
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
        <div   class="card">
          <div class="p-4 border-bottom">
            <div class="row"> 
              <div class="col-md-4 col-9">
                <h5 v-if="currentUser" class="font-size-15 mb-1"> {{ currentUser.name }}</h5>
                <p v-if="currentUser && currentUser.online_status" class="text-muted mb-0">
                  <i class="mdi mdi-circle text-success align-middle me-1"></i>
                  Active now
                </p>
                <p v-else class="text-muted mb-0">
                  <i class="mdi mdi-circle text-grey align-middle me-1"></i>
                  Offline
                </p>

              </div>
            </div>
          </div> 
          <div class="chat-conversation p-3">
            <div style="max-height: 470px; min-height: 470px;" class="overflow-auto simplebar-content-wrapper" id="containerElement" ref="chatContainer" @scroll="handleScrolling" >
              <ul class="list-unstyled" >
                <li v-for="(group, day) in groupedMessages.value" :key="day">
                  <div class="chat-day-title">
                    <span class="title">{{ day }}</span>
                  </div>
                  <li v-for="data in group" :key="data.id" :class="{ right: data.position === 'right' }">
                    <div class="conversation-list">
                      <div class="ctext-wrap">
                        <div class="conversation-name">{{ data.name }}</div>
                        <p>{{ data.content }}</p>
                        <p class="chat-time mb-0">
                          <i class="bx bx-time-five align-middle me-1 " ></i>
                          {{ data.time }}
                        </p>
                      </div>
                    </div>
                  </li>
                  <!-- <button v-if="showScrollButton" @click="scrollToBottom" class="scroll-down-button">
                    Scroll Down
                  </button> -->
                </li>
              </ul>
            </div>
          </div>
          <div class="chat-users">
            <div class="p-3 chat-input-section">
              <form @submit.prevent="chatForm" class="row">
                <div class="col">
                  <div class="position-relative">
                    <input autofocus :disabled="sending" type="text" v-model="formData.content" class="form-control chat-input rounded"
                      placeholder="Enter Message..." />
                  </div>
                </div>
                <div class="col-auto">
                  <button :disabled="sending" type="submit" class="btn btn-primary btn-rounded chat-send w-md">
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
<style>

</style>