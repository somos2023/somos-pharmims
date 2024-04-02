<script setup>
import { ref, onMounted, watchEffect,watch,computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';
import Vertical from "./vertical.vue";
import Horizontal from "./horizontal.vue";


const store = useStore();
const router = useRouter()
const route = useRoute()

const newExpireNotif = ref(0)

const token = computed(()=> store.state.user.token);
const notifList = computed(()=> store.state.notifList);
const isAlert = computed(()=> store.state.isAlert);

// store.dispatch('getSystemInfo');
store.dispatch('getUser');
store.dispatch('getDashboardData');

function showExpireAlert(){
  setTimeout(() => {
    if(newExpireNotif.value > 0 && isAlert.value == true){
      store.commit('setAlert', false);
      store.commit("alert", {
        show: true,
        title: 'Alert',
        icon: 'mdi-alert',
        classV: 'alert-danger show',
        message: "Attention: Some of our products are about to expire soon. Act now to ensure you make the most of these quality items before they reach their expiration date. ",
      });
    }

  }, 5000);

}

if(store.state.user.data.role_id != 3){
  store.dispatch('getNotifications')
  .then(({})=> {
    showExpireAlert()
  })
}

watchEffect(()=> {
  newExpireNotif.value = notifList.value.filter(notification => notification.status === 'unopened' && notification.type === 'expire_soon').length;
})


// const channelID = ref('')

if(store.state.user.data.role_id != 1 && store.state.chatData.users.length == 0 ){
  // store.dispatch('getChats', receiver)
  store.dispatch('getChatData')
  
  // }
}

const loader = ref(false);
const layoutType = ref('vertical');

const changeSidebar = (sidebar) => {
  // Define your logic for changing the sidebar here
};

const onRoutechange = () => {
  const layout = localStorage.getItem("layout");
  if (layout) {
    const parsedLayout = JSON.parse(layout);
    if (parsedLayout.loader === true) {
      loader.value = true;
    }
  }
};


let inactivityTimer;

function startInactivityTimer() {
  inactivityTimer = setTimeout(logout, 5 * 60 * 60 * 1000); // 6 hours in milliseconds
}

function resetInactivityTimer() {
  clearTimeout(inactivityTimer);
  startInactivityTimer();
}

function setupEventListeners() {
  document.addEventListener('mousemove', resetInactivityTimer);
  document.addEventListener('keydown', resetInactivityTimer);
  document.addEventListener('touchstart', resetInactivityTimer);

  // Handle browser close or tab/window close event
  window.addEventListener('beforeunload', function () {
    // Optionally perform some cleanup or show a confirmation message
    clearInterval(store.state.isChat);
    logout();
  });
}

function updateChat(){
  store.dispatch('getChatData')
}

const logout = () => {
  clearInterval(store.state.isChat);
  store.dispatch("logout").then(() => {
    router.push({ name: "user-selection" });
  });
};





onMounted(() => {
  if (localStorage.getItem("layout")) {
    let layout = localStorage.getItem("layout");
    layout = JSON.parse(layout);

    if (layout.width === "boxed") {
      document.body.setAttribute('data-layout-size', 'boxed');
    }

    if (layout.sidebar) {
      changeSidebar(layout.sidebar);
    }

    if (layout.mode === "dark") {
      document.body.setAttribute('data-layout-mode', 'dark');
    }
  }

  // set loader
  // setTimeout(() => {
  //   store.dispatch('getChatData')
  // }, 400);

  Pusher.logToConsole = true;

  var pusher = new Pusher('b41e019bfd8c998e8b54', {
    cluster: 'ap1'
  });

  var channel = pusher.subscribe(`chat-channel${store.state.user.data.id}`);
  channel.bind('user-chat', function(data) {
    store.commit('setNewMessage', JSON.parse(JSON.stringify(data.chat)));
  });

  var onlineChannel = pusher.subscribe('online-channel');
  onlineChannel.bind('user-online', function(data) {
    store.commit('setChatData', JSON.parse(JSON.stringify(data)));
    // updateChat()
  });

// let intervalId;

// intervalId = setInterval(function () {
//     if (!store.sending) {
      // store.dispatch('getChatData')
//       .catch(err=> {
//         clearInterval(intervalId);
//       });
//     }
//   }, 4000);

//   setupEventListeners();
//   startInactivityTimer();

  // Add a setTimeout after the initial interval
  // setTimeout(function () {
  //   clearInterval(intervalId); // Clear the existing setInterval after a specific time
  // }, 10000); // 60000 milliseconds = 60 seconds = 1 minute
});

// watchEffect(()=> {
//   if(token.value === null){
//     clearInterval(intervalId);
//   }
// })

// window.addEventListener('storage', (event) => {
//   if (event.key === 'TOKEN' && event.newValue === null) {
//     // Handle logout in other tabs
//     // You might want to redirect or trigger a logout action in your app
//     // console.log('Logout detected in another tab');
    
//   }
// });

// watchEffect(()=> {
  // channelID.value = computed(()=> store.state.user.data.id);
  // console.log(channelID.value)
// })

// onMounted(()=> {
//   // console.log(store.state.user.data.id)
 

    
// })



// export default {
//   created() {
   
//   },
//   // other Vue component lifecycle hooks
// };

</script>


<template>
  <!-- Loader -->
  <div id="preloader" v-if="loader">
    <div id="status">
      <div class="spinner-chase">
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
      </div>
    </div>
  </div>
  <div>
    <Vertical v-if="layoutType === 'vertical'">
      <slot :roleID="store.state.user.data.role_id" />
    </Vertical>

    <Horizontal v-if="layoutType === 'horizontal'">
      <slot /> 

    </Horizontal>

  </div>
</template>