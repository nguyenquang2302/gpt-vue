import { createApp } from 'vue'
import router from './router/index.js'
import App from './layouts/Login.vue'
import '../plugins/fontawesome-free/css/all.min.css'
import '../dist/css/adminlte.min.css'

import '../plugins/jquery/jquery.min.js'
import '../plugins/bootstrap/js/bootstrap.bundle.min.js'
import '../dist/js/adminlte.min.js'

import Axios from 'axios'
window.axios = Axios;

let app = createApp(App)
app.use(router)
app.mount('#app')