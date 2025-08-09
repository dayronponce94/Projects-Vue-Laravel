import { createApp } from 'vue'
import { createPinia } from 'pinia'
import mitt from 'mitt'; 

import App from './App.vue'
import router from './router'
import '@/assets/css/main.css'
import { Home, Users, Calendar, Stethoscope } from 'lucide-vue-next'

const app = createApp(App)
app.component('LucideHome', Home)
app.component('LucideUsers', Users)
app.component('LucideCalendar', Calendar)
app.component('LucideStethoscope', Stethoscope)

const emitter = mitt(); 
app.config.globalProperties.$emitter = emitter;

app.use(createPinia())
app.use(router)
app.mount('#app')