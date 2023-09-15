import { createApp } from 'vue'
import router from './router/index.js'
import App from './layouts/App.vue'

import '../plugins/fontawesome-free/css/all.min.css'
import  '../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'
import  '../plugins/datatables-responsive/css/responsive.bootstrap4.min.css'
import  '../plugins/datatables-buttons/css/buttons.bootstrap4.min.css'

import '../dist/css/adminlte.min.css'
import 'vue3-easy-data-table/dist/style.css'
import  '../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'
import "vue-select/dist/vue-select.css";

import '../plugins/jquery/jquery.min.js'

import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"

// import '../plugins/bootstrap/js/bootstrap.bundle.js'
// import '../dist/js/adminlte.js'
import '../dist/css/customize.css'

import 'vue3-toastify/dist/index.css'
import Vue3EasyDataTable from 'vue3-easy-data-table'
import { createPinia } from 'pinia'
import VueSelect  from "vue-select";
import VueNumberFormat from 'vue-number-format'

let app = createApp(App)
app.use(VueNumberFormat, {prefix: '', decimal: '.', thousand: '.,'})
app.use(router)

app.use(createPinia())
app.component('EasyDataTable', Vue3EasyDataTable)
app.component("v-select", VueSelect)
app.mount('#app')
