import axios from 'axios'
import router from '@/router'

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  baseURL: 'http://gpt.x/api',
  // baseURL: 'https://hotrothe.vn/api/',
  // timeout: 1000,
  // headers: {'X-Custom-Header': 'foobar'}
})

axiosIns.interceptors.request.use(
  async (config) => {
    config.headers['Authorization'] = 'Bearer ' + localStorage.getItem('accessToken')
    return config
  },
  (error) => {
    // Do something with request error
  }
)

axiosIns.interceptors.response.use(function (response) {
  // Do something with response data
  return response;
}, function (error) {
  // Do something with response error
  if (error.response.status === 401) {
    localStorage.removeItem('userData')
    localStorage.removeItem('userPermissions')
    localStorage.removeItem('accessToken')
    return router.replace('/login')
  } else {
    return error;
  }
});

export default axiosIns
