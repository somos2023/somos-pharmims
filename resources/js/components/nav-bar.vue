<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router';
import { ref, onMounted, computed, watch, watchEffect } from 'vue'

import defprofile from "../../images/def-avatar.png";

import simplebar from "simplebar-vue";
// Import images
// import us from '../../images/flags/us.jpg';
// import fr from '../../images/flags/french.jpg';
// import es from '../../images/flags/spain.jpg';
// import zh from '../../images/flags/chaina.png';
// import ar from '../../images/flags/arabic.png';
// import ph from '../../images/flags/philippines.png';

import logoDarkLg from '../../images/logo-dark.png';
import logoDarkSm from '../../images/logo.svg';
import logoLightLg from '../../images/logo-light.png';
import logoLightSm from '../../images/logo-light.svg';

// import avatar1 from '../../images/users/avatar-1.jpg';
// import avatar3 from '../../images/users/avatar-3.jpg';
// import avatar4 from '../../images/users/avatar-4.jpg';

// import github from '../../images/brands/github.png';
// import bitbucket from '../../images/brands/bitbucket.png';
// import dribbble from '../../images/brands/dribbble.png';
// import dropbox from '../../images/brands/dropbox.png';
// import mail_chimp from '../../images/brands/mail_chimp.png';
// import slack from '../../images/brands/slack.png';

import megamenu from '../../images/megamenu-img.png';

const store = useStore();
const router = useRouter()
const route = useRoute()

const userDetails = computed(()=> store.state.user.data);
const systemInfo = computed(()=> store.state.system);
const notifList = computed(()=> store.state.notifList);

const unopenedCount = ref(0);

watchEffect(()=> {
  unopenedCount.value = notifList.value.filter(notification => notification.status === 'unopened').length;
})

const emit = defineEmits(['toggle-menu', 'toggle-right-sidebar', 'hide-right-sidebar'])

const toggleMenu = (event, item) => {
  emit('toggle-menu', event, item)
}

const toggleRightSidebar = (event, item) => {
  emit('toggle-right-sidebar', event, item)
}

const hideRightSidebar = (event, item) => {
  emit('hide-right-sidebar', event, item)
}

// const logoDarkLg = '/path/to/logo-dark.png'; // Replace with the correct path
// const logoDarkSm = '/path/to/logo.svg';
// ... other logo imports ...

const languages = ref([
  // {
  //   flag: us,
  //   language: "en",
  //   title: "English",
  // },
  // {
  //   flag: ph,
  //   language: 'tl',
  //   title: 'Tagalog',
  // },
  // {
  //   flag: fr,
  //   language: "fr",
  //   title: "French",
  // },
  // {
  //   flag: es,
  //   language: "es",
  //   title: "Spanish",
  // },
  // {
  //   flag: zh,
  //   language: "zh",
  //   title: "Chinese",
  // },
  // {
  //   flag: ar,
  //   language: "ar",
  //   title: "Arabic",
  // },
]);

let lan = null;
let text = null;
let flag = null;
let value = null;

const logout = () => {
  store.dispatch("logout").then(() => {
    router.push({ name: "home" });
  });
};

// const toggleMenu = () => {
//   // You may need to handle this differently based on your component structure
// };

// const toggleRightSidebar = () => {
//   // You may need to handle this differently based on your component structure
// };

const initFullScreen = () => {
  document.body.classList.toggle("fullscreen-enable");
  if (!document.fullscreenElement &&
    !document.mozFullScreenElement &&
    !document.webkitFullscreenElement) {
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }
};

const setLanguage = (locale, country, flagSrc) => {
  lan = locale;
  text = country;
  flag = flagSrc;
  // Access i18n.locale and localStorage here
};

const changeLanguage = (selected) => {
  store.dispatch('changeLocalization', selected.language);
};

const sendMail = ()=> {
  store.dispatch("sendMail")
  .then(()=> {
    console.log('Success')
  });
}

onMounted(() => {
  // Initialization logic after the component is mounted
  // You can set initial values or perform other actions here
});

const isOpen = ref(false);
const openedNotification = ref({});

const openModal =(data)=> {
  if(data.status == 'unopened') {
    store.dispatch('changenotIfStatus', data.id)
    .then(()=> {
      data.status = 'viewed'
    })
  }
  
  openedNotification.value = data
  isOpen.value = !isOpen.value

}


</script>


<template>
  <header id="page-topbar">
    <div class="navbar-header">
      <div class="d-flex ">
        <!-- LOGO --> 
        <div class="navbar-brand-box">
          <router-link :to="{name: 'dashboard'}" class="logo logo-dark">
            <span class="logo-sm">
              <img :src="logoDarkSm" alt height="22" />
            </span>
            <span class="logo-lg">
              <img :src="logoDarkLg" alt height="17" />
            </span>
          </router-link>

          <router-link :to="{name: 'dashboard'}" class="logo logo-light">
            <span class="logo-sm">
              <img :src="systemInfo.logo" alt height="22" />
            </span>
            <span class="logo-lg">
              <img :src="systemInfo.logo_lg" alt height="19" />
            </span>
          </router-link>
        </div> 

        <button
          id="vertical-menu-btn"
          type="button"
          class="btn btn-sm px-3 font-size-16 header-item"
          @click="toggleMenu"
        >
          <i class="fa fa-fw fa-bars"></i> 
        </button>

        <!-- App Search-->
        <!-- <form class="app-search d-none d-lg-block ">
          <div class="position-relative">
            <input type="text" class="form-control" :placeholder="$t('navbar.search.text')" />
            <span class="bx bx-search-alt"></span>
          </div>
        </form> -->

       <div v-if="userDetails.role" class="d-flex justify-content-center align-items-center">
          <div class="text-center">
              <h4 class="m-0 p-0">Welcome, {{userDetails.role}}!</h4>
          </div>
      </div>

        <!-- <b-dropdown
          variant="black"
          class="dropdown-mega d-none d-lg-block ms-2"
          toggle-class="header-item"
          menu-class="dropdown-megamenu dropdown-menu-end"
        >
          <template v-slot:button-content>
            {{ $t('navbar.dropdown.megamenu.text') }}
            <i class="mdi mdi-chevron-down"></i>
          </template>

          <div class="row">
            <div class="col-sm-8">
              <div class="row">
                <div class="col-md-4">
                  <h5 class="font-size-14 mt-0">{{ $t('navbar.dropdown.megamenu.uicontent.title') }}</h5>
                  <ul class="list-unstyled megamenu-list">
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.lightbox') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.rangeslider') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.sweetalert') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.rating') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.forms') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.tables') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.charts') }}</a>
                    </li>
                  </ul>
                </div>

                <div class="col-md-4">
                  <h5
                    class="font-size-14 mt-0"
                  >{{ $t('navbar.dropdown.megamenu.application.title') }}</h5>
                  <ul class="list-unstyled megamenu-list">
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.application.list.ecommerce') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.application.list.calendar') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.application.list.email') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.application.list.projects') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.application.list.tasks') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.application.list.contacts') }}</a>
                    </li>
                  </ul>
                </div>

                <div class="col-md-4">
                  <h5
                    class="font-size-14 mt-0"
                  >{{ $t('navbar.dropdown.megamenu.extrapages.title') }}</h5>
                  <ul class="list-unstyled megamenu-list">
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.lightsidebar') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.compactsidebar') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.horizontallayout') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.maintenance') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.comingsoon') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.timeline') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.extrapages.list.faqs') }}</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="row">
                <div class="col-sm-6">
                  <h5 class="font-size-14 mt-0">{{ $t('navbar.dropdown.megamenu.uicontent.title') }}</h5>
                  <ul class="list-unstyled megamenu-list">
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.lightbox') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.rangeslider') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.sweetalert') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.rating') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.forms') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.tables') }}</a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0);"
                      >{{ $t('navbar.dropdown.megamenu.uicontent.list.charts') }}</a>
                    </li>
                  </ul>
                </div>

                <div class="col-sm-5">
                  <div>
                    <img
                      :src="megamenu"
                      alt
                      class="img-fluid mx-auto d-block"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-dropdown> -->
      </div>

      <div class="d-flex ">
        <!-- <b-dropdown
          class="d-inline-block d-lg-none ms-2"
          variant="black"
          menu-class="dropdown-menu-lg p-0"
          toggle-class="header-item noti-icon"
          right
        >
          <template v-slot:button-content>
            <i class="mdi mdi-magnify"></i>
          </template>

          <form class="p-3">
            <div class="form-group m-0">
              <div class="input-group">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Search ..."
                  aria-label="Recipient's username"
                />
                <div class="input-group-append">
                  <button class="btn btn-primary" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </b-dropdown> -->

        <!-- <b-dropdown variant="white" right toggle-class="header-item">
          <template v-slot:button-content>
            <img class :src="flag" alt="Header Language" height="16" />
          </template>
          <b-dropdown-item
            class="notify-item"
            v-for="(entry, i) in languages"
            :key="`Lang${i}`"
            :value="entry"
            @click="setLanguage(entry.language, entry.title, entry.flag)"
            :class=" {'active' : lan === entry.language}"
          >
            <img :src="`${entry.flag}`" alt="user-image" class="me-1" height="12" />
            <span class="align-middle">{{ entry.title }}</span>
          </b-dropdown-item>
        </b-dropdown> -->

         <!-- <b-dropdown variant="white" right toggle-class="header-item">
          <template v-slot:button-content>
            <img class :src="flag" alt="Header Language" height="16" />
          </template>
          <b-dropdown-item
            class="notify-item"
            v-for="(entry, i) in languages"
            :key="`Lang${i}`"
            :value="entry"
            @click="changeLanguage(entry)"
            :class=" {'active' : lan === entry.language}"
          >
            <img :src="`${entry.flag}`" alt="user-image" class="me-1" height="12" />
            <span class="align-middle">{{ entry.title }}</span>
          </b-dropdown-item>

        </b-dropdown> -->

        <!-- <b-dropdown
          class="d-none d-lg-inline-block noti-icon"
          menu-class="dropdown-menu-lg dropdown-menu-end"
          right
          toggle-class="header-item"
          variant="black"
        >
          <template v-slot:button-content>
            <i class="bx bx-customize"></i>
          </template>

          <div class="px-lg-2">
            <div class="row no-gutters">
              <div class="col">
                <a class="dropdown-icon-item" href="javascript:void(0);">
                  <img :src="github" alt="Github" />
                  <span>{{ $t('navbar.dropdown.site.list.github') }}</span>
                </a>
              </div>
              <div class="col">
                <a class="dropdown-icon-item" href="javascript: void(0);">
                  <img :src="bitbucket" alt="bitbucket" />
                  <span>{{ $t('navbar.dropdown.site.list.github') }}</span>
                </a>
              </div>
              <div class="col">
                <a class="dropdown-icon-item" href="javascript: void(0);">
                  <img :src="dribbble" alt="dribbble" />
                  <span>{{ $t('navbar.dropdown.site.list.dribbble') }}</span>
                </a>
              </div>
            </div>

            <div class="row no-gutters">
              <div class="col">
                <a class="dropdown-icon-item" href="javascript: void(0);">
                  <img :src="dropbox" alt="dropbox" />
                  <span>{{ $t('navbar.dropdown.site.list.dropbox') }}</span>
                </a>
              </div>
              <div class="col">
                <a class="dropdown-icon-item" href="javascript: void(0);">
                  <img :src="mail_chimp" alt="mail_chimp" />
                  <span>{{ $t('navbar.dropdown.site.list.mailchimp') }}</span>
                </a>
              </div>
              <div class="col">
                <a class="dropdown-icon-item" href="javascript: void(0);">
                  <img :src="slack" alt="slack" />
                  <span>{{ $t('navbar.dropdown.site.list.slack') }}</span>
                </a>
              </div>
            </div>
          </div>
        </b-dropdown> -->

        <div class="dropdown d-none d-lg-inline-block ms-1">
          <button type="button" class="btn header-item noti-icon" @click="initFullScreen">
            <i class="bx bx-fullscreen"></i>
          </button>
        </div>

        <b-dropdown
          v-if="userDetails.role_id !== 3"
          right
          menu-class="dropdown-menu-lg p-0 dropdown-menu-end"
          toggle-class="header-item noti-icon"
          variant="black"
        >
          <template v-slot:button-content>
            <i class="bx bx-bell bx-tada"></i>
            <span
              v-if="unopenedCount != 0"
              class="badge bg-danger rounded-pill"
            >{{ unopenedCount }}</span>
          </template>

          <div class="p-3">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="m-0">{{ $t('navbar.dropdown.notification.text')}}</h6>
              </div>
              <div class="col-auto">
                <!-- view all -->
                <router-link v-if="notifList.length > 0" to="/notification" class="small">{{ $t('navbar.dropdown.notification.subtext')}}</router-link> 
              </div>
            </div>
          </div>
          <simplebar v-if="notifList.length > 0" style="max-height: 230px;">
            <a href="javascript: void(0);" @click="openModal(item)" v-for="item in notifList" :key="item.id" class="text-reset ">
              <div class="d-flex p-2" :class="{'notification': item.status == 'unopened'}" >
                <div class="avatar-xs me-3 ">
                  <span class="avatar-title bg-danger rounded-circle font-size-16">
                    <i class='bx bxs-hourglass'></i> 
                  </span>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mt-0 mb-1">{{item.message}}</h6>
                  <div class="font-size-12 text-gray" :class="{'text-white': item.status == 'unopened'}" >
                    <p class="mb-1">Order #: {{item.stock.order_number}}</p>
                    <p class="mb-1">Brand Name: {{item.product.brand_name}}</p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ item.date_time }} 
                    </p>
                  </div>
                </div>
              </div>
            </a>
           
          </simplebar>
          <div v-else class="p-2 border-top d-grid">
            You don't have notifications yet
          </div>
         <!-- <div class="p-2 border-top d-grid">
            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
              <i class="mdi mdi-arrow-down-circle me-1"></i>
              <span key="t-view-more"> {{ $t('navbar.dropdown.notification.button')}} </span>
            </a>
          </div> -->
        </b-dropdown>

        <b-dropdown right variant="black" toggle-class="header-item" menu-class="dropdown-menu-end">
          <template v-slot:button-content>
            <img
              class="rounded-circle header-profile-user"
              :src="userDetails.image ? userDetails.image : defprofile"
              alt="Header Avatar"
            />
            <span class="d-none d-xl-inline-block ms-1"> {{ userDetails.first_name }}</span>
            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
          </template>
          <!-- item-->
      
          <b-dropdown-item href="/profile">
            <i class="bx bx-user font-size-16 align-middle me-1"></i>
            {{ $t('navbar.dropdown.usermenu.list.profile') }}
          </b-dropdown-item>
        
          <b-dropdown-item v-if="userDetails.role_id !== 3" to="/settings">
            <i class="bx bx-cog font-size-16 align-middle me-1"></i>
            Settings
          </b-dropdown-item>
         <!--  <b-dropdown-item class="d-block" href="javascript: void(0);">
            <span class="badge bg-success float-end">11</span>
            <i class="bx bx-wrench font-size-16 align-middle me-1"></i>
            {{ $t('navbar.dropdown.usermenu.list.settings') }}
          </b-dropdown-item> -->
          <!-- <b-dropdown-item href="javascript: void(0);">
            <i class="bx bx-lock-open font-size-16 align-middle me-1"></i>
            {{ $t('navbar.dropdown.usermenu.list.lockscreen') }}
          </b-dropdown-item> -->
          <b-dropdown-divider></b-dropdown-divider>
          <a href="javascript:void(0)" @click="logout()" class="dropdown-item text-danger">
            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
            {{ $t('navbar.dropdown.usermenu.list.logout') }}
          </a>
          
        </b-dropdown>

       <!--  <div class="dropdown d-inline-block">
          <button
            type="button"
            class="btn header-item noti-icon right-bar-toggle toggle-right"
            @click="toggleRightSidebar"
          >
            <i class="bx bx-cog bx-spin toggle-right"></i>
          </button>
        </div> -->
      </div>
    </div>
  </header>
  <b-modal v-model="isOpen"  size="md" centered title="Notification">
    <h5 class="mt- mb-3  text-primary">{{openedNotification.message}}</h5>
    <div v-if="Object.keys(openedNotification).length > 0" class="row">
      <div class="col-xl-6">
        <div class="product-detai-imgs">
          <div class="product-img">
            <img
              :src="openedNotification.product.image"
              alt
              class="img-fluid mx-auto d-block"
            />
          </div>
        </div>
      </div>
      <div class="col-xl-6">
        <div class="mt-3">
          <h4 class="mt- mb-1">{{openedNotification.product.brand_name}}</h4>
          <p class="text-muted mb-3 mt-3 float-left me-3">Barcode: {{openedNotification.stock.barcode}}</p>
          
          <div class="row mb-3">
            <div class="col-md-12">
              <div>
                <p class="text-muted mb-3 float-left me-3">
                  Order #: {{openedNotification.stock.order_number}}
                </p>
                <p class="text-muted">
                  Stock: {{openedNotification.stock.quantity}}
                </p>
                <p class="text-muted">
                  Expire Date : {{openedNotification.stock.expires_at}}
                </p>
                
              </div>
            </div>
          </div>
        </div>
      </div>
         <!-- {{openedNotification.product.image}} -->
    </div> 
     <template #footer>
          <div class="w-100"></div>
        </template>
  </b-modal>

</template>

<style>
  .notification {
    background-color: #5DADE2 ;
    border-bottom: 2px solid #fff;
    color: white;
  }
</style>