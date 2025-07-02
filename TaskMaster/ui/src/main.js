import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
import { authStore } from './stores/auth';

const app = createApp(App);

app.provide('authStore', authStore);

app.use(router).mount('#app');