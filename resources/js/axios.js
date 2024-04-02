
import axios from "axios";
import store from "./store";
import router from "./router";

const axiosClient = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
})

axiosClient.interceptors.request.use(config => {
  config.headers.Authorization = `Bearer ${store.state.user.token}`
  return config;
})

axiosClient.interceptors.response.use(response => {
  return response;
}, error => {
  if (error.response.status === 401) {
    localStorage.removeItem('TOKEN')
    router.push({name: 'login'})
  } else if (error.response.status === 404) {
    router.push({name: 'not-found'})
  } else if (error.response.status === 403) {
    router.push({name: 'forbidden'})
  }
  //  else if (error.response.status === 500) {
  //   router.push({name: 'server-error'})
  // }
  throw error;
})

export default axiosClient;

