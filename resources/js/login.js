import { createApp } from 'vue'
import App from './layouts/Login.vue'
import '../plugins/fontawesome-free/css/all.min.css'
import '../dist/css/adminlte.min.css'

import Axios from 'axios'
window.axios = Axios;

let app = createApp(App)
app.mount('#app')