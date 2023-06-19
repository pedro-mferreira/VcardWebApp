import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'
import Toaster from "@meforma/vue-toaster"
import VueSocketIO from 'vue-3-socket.io'

import FieldErrorMessage from "./components/global/FieldErrorMessage.vue"
import ConfirmationDialog from "./components/global/ConfirmationDialog.vue"

const socketIO = new VueSocketIO({
    debug: true,
    connection: 'http://localhost:8080',
   })

let toastOptions = {
    position: 'top',
    timeout: 3000,
    pauseOnHover: true
}
const app = createApp(App).use(store).use(socketIO).use(router).use(Toaster, toastOptions)

axios.defaults.baseURL = "http://localhost:8000/api"
app.config.globalProperties.$axios = axios
app.config.globalProperties.$serverUrl = "http://localhost:8000"


app.component('field-error-message', FieldErrorMessage)
app.component('confirmation-dialog', ConfirmationDialog)
store.$socket=socketIO.io
store.$toast=app.$toast
app.mount('#app')


