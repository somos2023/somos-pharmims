import { createStore } from "vuex";
import axiosClient from "../axios";

const store = createStore({
  state: {
    isAlert: sessionStorage.getItem("ALERT") ?? true,
    isChat: null,
    isAdmin: localStorage.getItem("ADMIN"),
    isStaff: localStorage.getItem("STAFF"),
    isSupplier: localStorage.getItem("SUPPLIER"),
    sending: false,
    chatData: {
      contacts: [],
      messages: [],
      users: [],
      recents: [],
      current: sessionStorage.getItem("RECEIVER") ?? 0,
      loading: false
    },
    interval_time: 10000,
    loginAttempt: {
      email: sessionStorage.getItem("EMAIL"),
      disabled: false,
      count: 0,
      countdown: 0
    },
    notify: {
      show: false,
      title: '',
      icon: '',
      class: '',
      message: []
    },
    dashboard: {
      data: [],
      loading: false
    },
    system: localStorage.getItem("SYSTEM") ? JSON.parse(localStorage.getItem("SYSTEM")) : {name: ""} ,
    user: {
      data: localStorage.getItem("USER") ? JSON.parse(localStorage.getItem("USER")) : {},
      token: localStorage.getItem("TOKEN"),
    },
    userList: {
      data: [],
      loading: false
    },
    currentUser: {},
    productList: {
      data: [],
      loading: false
    },
    currentProduct: {},
    chatList: {
      data: [],
      current_receiver: sessionStorage.getItem("RECEIVER") ?? 0,
      loading: false
    },
    stockList: {
      data: [],
      loading: false
    },

    supplierProductList: {
      data: [],
      loading: false
    },
    currentSupplierProduct: {},
    checkout: sessionStorage.getItem("CHECKOUT") ?? false,
    placeOrder: sessionStorage.getItem("PLACEORDER") ?? false,
    cartList: {
      data: [],
      loading: false
    },

    shopOrders: {
      data: [],
      loading: false
    },
    currentShopOrders: [],
    shopOrderHistory: {
      data: [],
      loading: false
    },
    myOrders: {
      data: [],
      loading: false
    },
    myOrderHistory: {
      data: [],
      loading: false
    },
    orderDetails: {},
    
    transactionList: {
      data: [],
      loading: false
    },
    currentTransaction: sessionStorage.getItem("TRANSACTION") ?? null,
    transactionDetails: {},
    salesReport: {
      data: [],
      totals: {},
      loading: false
    },
    notifList: []
    
  },
  actions: {
    sendMail({commit}, data) {
      commit('setSending', true);
      return axiosClient.post('/send-mail', data).then((res) => {
        commit('setSending', false);
        return res;
      });
    },
    sendForgotPassword({}, email) {
      commit('setSending', true);
      return axiosClient.post('/forget-password', email).then((res) => {
        commit('setSending', false);
        return res;
      });
    },
    getSystemInfo({commit}) {
      commit('setSending', true);
      return axiosClient.get('/system-info')
        .then(({data}) => {
          commit('setSending', false);
          commit('setSystemInfo', data.data);
          return data;
        });
    },
    login({commit}, user) {
      return axiosClient.post('/login', user)
        .then((res) => {
          // commit('setUser', res.data.user);
          commit('setToken', res.data.token);
          return res;
        })
    },
    saveLoginAttempt({commit}, login) {
      let response;
      if (login.id) {
        response = axiosClient
          .put(`/login-attempt/${login.id}`, login)
          .then((res) => {
            return res;
          });
      } else {
        response = axiosClient.post("/login-attempt", login)
          .then((res) => {
            return res;
        });
      }

      return response;
    },
    updateCountdown({commit}, login) {
      let response;
      if (login.id) {
        response = axiosClient.post(`/login-attempt/${login.id}`, login);
      } 
      return response;
    },
    getLoginAttempt({commit}, email) {
      return axiosClient.get(`/login-attempt/${email}`,)
        .then((res) => {
          if(res.data.success){
            commit('setLoginAttempt', res.data.data);
          }
          return res;
        })
    },
    logout({commit}) {
      // commit('logout');
      commit('setSending', true);
      return axiosClient.post('/logout')
        .then(response => {
          commit('setSending', false);
          commit('logout');
          return response;
        })
    },
    getUser({commit}) {
      commit('setSending', true);
      return axiosClient.get('/auth-user')
      .then(res => {
        commit('setSending', false);
        commit('setUser', res.data.data);
      });
    },
    saveProfile({commit}, profile) {
      commit('setSending', true);
      return axiosClient
          .put(`/profile/${profile.id}`, profile)
          .then((res) => {
            commit('setSending', false);
            return res;
        })
    },
    changePassword({commit}, password) {
      commit('setSending', true);
      return axiosClient
        .put(`/change-password/${password.id}`, password)
        .then((res) => {
          commit('setSending', false);
          return res;
      })
    },
    changeMyInfo({commit}, profile) {
      commit('setSending', true);
      return axiosClient
          .put(`/change-my-info/${profile.id}`, profile)
          .then((res) => {
            commit('setSending', false);
            
            return res;
        })
    },
    saveUser({commit}, form) {
      commit('setSending', true);
      let response;
      if (form.id) {
        response = axiosClient
          .put(`/user/${form.id}`, form)
          .then((res) => {
            // commit('setUserList', res.data.data)
            commit('setSending', false);
            return res;
          });
      } else {
        response = axiosClient.post("/user", form).then((res) => {
          // commit('setUserList', res.data.data)
          commit('setSending', false);
          return res;
        });
      }

      return response;
    },
    getCurrentUser({commit}, id) {
      commit('setSending', true);
      return axiosClient.get(`/user/${id}`)
      .then(res => {
        commit('setSending', false);
        commit('setCurrentUser', res.data.data[0])
        return res;
      });
    },
    getUserList({commit}) {
      commit('setSending', true);
      commit("setUserListLoading", true);
      return axiosClient.get('/user').then((res) => {
        commit('setSending', false);
        commit("setUserList", res.data.data);
        commit("setUserListLoading", false);
      });
    },
    deleteUser({ commit }, id) {
      commit('setSending', true);
      return axiosClient.delete(`/user/${id}`)
        .then(response => {
          commit('setSending', false);
          return response;
        });
    },
    changeUserStatus({ commit }, data) {
      commit('setSending', true);
      return axiosClient.post(`/change-user-status/${data.id}`, data)
        .then((res) => {
          commit('setSending', false);
          // commit("setUserList", res.data.data);
          return res;
        });
    },
    getProductList({commit}) {
      commit('setSending', true);
      commit("setProductListLoading", true);
      return axiosClient.get('/product').then((res) => {
        commit('setSending', false);
        commit("setProductList", res.data.data);
        commit("setProductListLoading", false);
      });
    },
    deleteProduct({ commit }, id) {
      commit('setSending', true);
      return axiosClient.delete(`/product/${id}`)
        .then(response => {
          commit('setSending', false);
          return response;
        });
    },
    saveProduct({commit}, form) {
      commit('setSending', true);
      let response;
      if (form.id) {
        response = axiosClient
          .put(`/product/${form.id}`, form)
          .then((res) => {
            commit('setSending', false);
            // commit('setProductList', res.data.data)
            return res;
          });
      } else {
        response = axiosClient.post("/product", form).then((res) => {
          commit('setSending', false);
          // commit('setProductList', res.data.data)
          return res;
        });
      }

      return response;
    },
    getCurrentProduct({commit}, id) {
      commit('setSending', true);
      return axiosClient.get(`/product/${id}`)
      .then(res => {
        commit('setSending', false);
        commit('setCurrentProduct', res.data.data[0])
        return res;
      });
    },
    getStocks({commit}) {
      commit('setSending', true);
      commit("setStockLoading", true);
      return axiosClient.get(`/stocks`).then(res => {
        commit("setStockData", res.data.data);
        commit("setStockLoading", false);
        commit('setSending', false);
      });
    },
    getDashboardData({commit}) {
      commit('setSending', true);
      commit("setDashboardLoading", true);
      return axiosClient.get('/dashboard').then((res) => {
        commit("setDashboard", res.data.data);
        commit("setDashboardLoading", false);
        commit('setSending', false);
      });
    },
    
    // saveChat({commit}, form) {
    //   return axiosClient.post('/chat', form)
    //   .then(res => {
    //       // commit('setNewMessage', res.data.message)
    //     return res;
    //   });
    // },
    getChatData({commit}) {
      commit("setChatDataLoading", true);
      return axiosClient.get('/chat-app').then(res => {
        commit("setChatData", res.data);
        commit("setChatDataLoading", false);
      });
    },

    saveMessage({commit}, form) {
      return axiosClient.post('/chat-app', form)
      .then(res => {
          commit('setNewMessage', res.data.data)
        return res;
      });
    },

    getMessages({commit}, data) {
      return axiosClient.post('/get-messages', data).then(res => {
        commit("setChatMessages", res.data.messages);
      });
    },

    getSupplierProducts({commit}) {
      commit('setSending', true);
      commit("setSuppProductListLoading", true);
      return axiosClient.get('/supplier-product').then((res) => {
        commit("setSuppProductList", res.data.data);
        commit("setSuppProductListLoading", false);
        commit('setSending', false);
      });
    },
    saveToCart({commit}, item) {
      commit('setSending', true);
      let response;
      if (item.id) {
        response = axiosClient.put(`/cart/${item.id}`, item).then((res) => {
          commit('setSending', false);
          return res;
        });
      } else {
        response = axiosClient.post('/cart', item).then((res) => {
          commit('setSending', false);
          return res;
        });
      }
      return response;
    },
    getCart({commit}) {
      commit('setSending', true);
      commit("setCartLoading", true);
      return axiosClient.get(`/cart`).then(res => {
        commit('setSending', false);
        commit("setCartData", res.data.data);
        commit("setCartLoading", false);
      });
    },
    deleteCartItem({ commit }, id) {
      commit('setSending', true);
      return axiosClient.delete(`/cart/${id}`)
        .then(response => {
          commit('setSending', false);
          return response;
        });
    },

    saveOrder({commit}, form) {
      commit('setSending', true);
      let response;
      if (form.id) {
        response = axiosClient.put(`/order/${form.id}`, form).then((res) => {
          commit('setSending', false);
          return res;
        });
      } else {
        response = axiosClient.post('/order', form).then((res) => {
          commit('setSending', false);
          return res;
        });
      }
      return response;
    },
    saveBuyNow({commit}, form) {
      commit('setSending', true);
      return axiosClient.post('/buy-now', form).then((res) => {
        commit('setSending', false);
        return res;
      });
    },

    getMyOrder({commit}) {
      commit('setSending', true);
      commit("setMyOrdersLoading", true);
      return axiosClient.get('/my-order').then(res => {
        commit('setSending', false);
        commit("setMyOrders", res.data.data);
        commit("setMyOrdersLoading", false);
      });
    },
    getShopOrder({commit}) {
      commit('setSending', true);
      commit("setMyOrdersLoading", true);
      return axiosClient.get('/shop-order').then(res => {
        commit('setSending', false);
        commit("setMyOrders", res.data.data);
        commit("setMyOrdersLoading", false);
      });
    },

    getShopOrders({commit}) {
      commit('setSending', true);
      commit("setShopOrderDataLoading", true);
      return axiosClient.get(`/get-shop-order`).then(res => {
        commit('setSending', false);
        commit("setShopOrderData", res.data);
        commit("setShopOrderLoading", false);
      });
    },

    getOrderDetails({commit}, id) {
      commit('setSending', true);
      return axiosClient.get(`/order/${id}`)
      .then(res => {
        commit('setSending', false);
        commit('setOrderDetails', res.data.data);
      });
    },

    saveTransaction({commit}, form) {
      commit('setSending', true);
      let response;
      if (form.id) {
        response = axiosClient.put(`/transaction/${form.id}`, form).then((res) => {
          commit('setSending', false);
          return res;
        });
      } else {
        response = axiosClient.post('/transaction', form).then((res) => {
          commit('setSending', false);
          return res;
        });
      }
      return response;
    },

    getTransactionList({commit}) {
      commit('setSending', true);
      commit("setTransactionLoading", true);
      return axiosClient.get('/transaction').then(res => {
        commit('setSending', false);
        commit("setTransactionData", res.data.data);
        commit("setTransactionLoading", false);
      });
    },
    getTransactionDetails({commit}, id) {
      commit('setSending', true);
      return axiosClient.get(`/transaction/${id}`)
      .then(res => {
        commit('setSending', false);
        commit('setTransactionDetails', res.data.data[0]);
      });
    },

    saveSystemInfo({commit}, data) {
      commit('setSending', true);
      let response;
      if(data.id){
        response = axiosClient
          .put(`/system/${data.id}`, data).then((res) => {
            commit('setSending', false);
            if(res.data.data){
              commit("setSystemInfo", res.data.data);
            }
            
            return res;
          });
      } else {
        response = axiosClient.post("/system", data).then((res) => {
          commit('setSending', false);
            if(res.data.data){
              commit("setSystemInfo", res.data.data);
            }
          return res;
        });
      }
      return response;
    },

    updateLastLogin({}) {
       return axiosClient.post("/last-login").then((res) => {
          return res;
        });
    },

    getOrderSales({commit}) {
      commit('setOrderSalesLoading', true);
      return axiosClient.get("/order-sales").then((res) => {
        commit('setOrderSales', res.data);
        commit('setOrderSalesLoading', false);
        return res;
      });
    },

    getTransactionSales({commit}) {
      commit('setTransactionSalesLoading', true);
      return axiosClient.get("/transaction-sales").then((res) => {
        commit('setTransactionSales', res.data);
        commit('setTransactionSalesLoading', false);
        return res;
      });
    },

    getFilteredOrderSales({commit}, data) {
      return axiosClient.post("/order-sales", data).then((res) => {
        commit('setOrderSales', res.data);
        return res;
      });
    },

    getFilteredTransactionSales({commit}, data) {
      return axiosClient.post("/transaction-sales", data).then((res) => {
        commit('setTransactionSales', res.data);
        return res;
      });
    },

    getNotifications({commit}) {
      return axiosClient.get("/get-notif").then((res) => {
        commit('setNotifList', res.data.data);
        return res;
      });
    },

    changenotIfStatus({commit}, id) {
      return axiosClient.put(`/change-notif-status/${id}`).then((res) => {
        return res;
      });
    }
    // getCurrentChat({commit}, id) {
    //   return axiosClient.get(`/chat/${id}`)
    //   .then(res => {
    //     commit('setCurrentChat', res.data)
    //     return res;
    //   });
    // },
    // updateCurrentMessage({commit}, id) {
    //   return axiosClient.get(`/chat/${id}`)
    //   .then(res => {
    //     commit('setNewMessage', res.data.message)
    //     return res;
    //   });
    // },
    
    // downloadExcel({commit}, data) {
    //   return axiosClient.get(`/download-excel/${data.model}`).then(res => {
    //     return res;
    //   });
    // },
    
    
  },
  mutations: {
    disableLogin: (state, value) => {
      state.loginAttempt.disabled = value;
    },
    setLoginAttempt: (state, data) => {
      state.loginAttempt = data;
      sessionStorage.setItem("EMAIL", data.email);
    },
    setLoginEmail: (state, email) => {
      state.loginAttempt.email = email;
      sessionStorage.setItem("EMAIL", email);
    },
    countdownLogin: (state, count) => {
      state.loginAttempt.countdown = count;
    },
    addLoginError: (state) => {
      state.loginAttempt.count += 1;
    },
    notify: (state, {title, icon, classV, message}) => {
      state.notify.show = true;
      state.notify.title = title;
      state.notify.icon = icon;
      state.notify.class = classV;
      state.notify.message = message;
      setTimeout(() => {
        state.notify.show = false;
      }, 3000)
    },
    alert: (state, {title, icon, classV, message}) => {
      state.notify.show = true;
      state.notify.title = title;
      state.notify.icon = icon;
      state.notify.class = classV;
      state.notify.message = message;
    },
    setSystemInfo: (state, data) => {
      state.system = data ;
      if(data){
        localStorage.setItem('SYSTEM', JSON.stringify(data));
      }
    },
    setUser: (state, data) => {
      state.user.data = data;
      if(data){
        localStorage.setItem('USER', JSON.stringify(data));
      }
      
    },
    setToken: (state, token) => {
      state.user.token = token;
      localStorage.setItem('TOKEN', token);
    },
    logout: (state) => {
      state.chatData.messages = []
      store.state.chatData.current = 0;
      state.user.token = null;
      state.user.data = {};

      state.isAdmin = null;
      state.isStaff = null;
      state.isSupplier = null;
      state.isAlert = true;
      localStorage.removeItem("TOKEN");
      sessionStorage.removeItem("EMAIL");
      sessionStorage.removeItem("RECEIVER");
      sessionStorage.removeItem("ALERT");
      localStorage.removeItem("ADMIN")
      localStorage.removeItem("STAFF")
      localStorage.removeItem("SUPPLIER")
    },
    setCurrentUser: (state, user) => {
      state.currentUser = user;
    },
    setUserList: (state, user) => {
      state.userList.data = user ?? [];
    },
    setUserListLoading: (state, load) => {
      state.userList.loading = load;
    },
    setProductList: (state, prod) => {
      state.productList.data = prod ?? [];
    },
    setProductListLoading: (state, load) => {
      state.productList.loading = load;
    },
    setCurrentProduct: (state, data) => {
      state.currentProduct = data;
    },
    filterProduct: (state, id) => {
      state.productList.data = state.productList.data.filter(item => item.id !== id);
    },
    setStockData: (state, data) => {
        state.stockList.data = data;
    },
    setStockLoading: (state, load) => {
        state.stockList.loading = load;
    },
    setDashboard: (state, data) => {
      state.dashboard.data = data;
    },
    setDashboardLoading: (state, load) => {
      state.dashboard.loading = load;
    },
    setChatData: (state, data) => {
      state.chatData.users = data.users;
      // state.chatData.contacts = data.contacts;
      // state.chatData.recents = data.recents;
      if(state.chatData.current == 0 && data.users != null){
        state.chatData.messages = []
        state.chatData.current = data.users[0].id;
        sessionStorage.setItem("RECEIVER", data.users[0].id);
      }
    },
    setChatDataLoading: (state, load) => {
      state.chatData.loading = load;
    },
    setCurrentReceiver: (state, id) => {
      state.chatData.current = id;
      sessionStorage.setItem("RECEIVER", id);
    },
    setChatMessages: (state, messages) => {
      state.chatData.messages = messages;
    },
    setNewMessage: (state, data) => {
        state.chatData.messages.push(data);
    },

    setSuppProductList: (state, prod) => {
      state.supplierProductList.data = prod;
    },
    setBuyNow: (state, data) => {
      sessionStorage.setItem("BUYNOW", JSON.stringify(data));
    },
    setCheckout: (state, data) => {
      sessionStorage.setItem("CHECKOUT", data);
      state.checkout = data ?? false;
    },
    setPlaceOrder: (state, data) => {
      sessionStorage.setItem("PLACEORDER", data)
      state.placeOrder = data ?? false;
    },
    setCartData: (state, data) => {
      state.cartList.data = data;
    },
    setCartLoading: (state, load) => {
      state.cartList.loading = load;
    },

    setMyOrders: (state, data) => {
      state.myOrders.data = data;
    },
    setMyOrdersLoading: (state, load) => {
      state.myOrders.loading = load;
    },
    setOrderDetails: (state, data) => {
      state.orderDetails = data;
    },
    setTransaction: (state, data) => {
      sessionStorage.setItem("TRANSACTION", JSON.stringify(data));
      state.currentTransaction = data;
    },
    setTransactionData: (state, data) => {
      state.transactionList.data = data;
    },
    setTransactionLoading: (state, loading) => {
      state.transactionList.loading = loading;
    },
    setTransactionDetails: (state, data) => {
      state.transactionDetails = data;
    },

    setSuppProductListLoading: (state, load) => {
      state.supplierProductList.loading = load;
    },
    setShopOrderData: (state, data) => {
      state.shopOrders.data = data;
    },
    setShopOrderDataLoading: (state, load) => {
      state.shopOrders.loading = load;
    },

    setOrderSales: (state, data) => {
      state.salesReport.data = data.data;
      state.salesReport.totals = data.totals;
    },
    setOrderSalesLoading: (state, loading) => {
      state.salesReport.loading = loading;
    },
    setTransactionSales: (state, data) => {
      state.salesReport.data =  data.data;
      state.salesReport.totals = data.totals;
    },
    setTransactionSalesLoading: (state, loading) => {
      state.salesReport.loading = loading;
    },

    setSending: (state, status) => {
      state.sending = status;
    },
    setNotifList: (state, data) => {
      state.notifList = data;
    },

    setIsRole: (state, role) => {
      if(role === 1){
        localStorage.setItem("ADMIN", true)
        state.isAdmin = true;
      } else if(role === 2){
        localStorage.setItem("STAFF", true)
        state.isStaff = true;
      } else if(role === 3){
        localStorage.setItem("SUPPLIER", true)
        state.isSupplier = true;
      }
    },

    setChat: (state, status) => {
      state.isChat = status;
    },

    setAlert: (state, status) => {
      state.isAlert = status;
      sessionStorage.setItem("ALERT", status);
    },


    // setCurrentChat: (state, data) => {
    //   state.currentChat = data;
    // },
    

  },
  getters: {},
  modules: {},

});

export default store;
