import { createApp } from 'vue'
import router from './router'
import AppLayout from './layouts/AppLayout.vue'

import './style.css'

const app = createApp(AppLayout)
app.use(router)
app.mount('#app')
