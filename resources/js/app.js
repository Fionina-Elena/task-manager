import './bootstrap';
import 'mdb-vue-ui-kit';
import Alpine from 'alpinejs';
import { createApp } from 'vue';
import TaskManager from './components/TaskManager.vue';
import { routes } from './routes.js'; 
import { createRouter, createWebHistory } from 'vue-router'; 


const router = createRouter({ 
    history: createWebHistory(),
    routes: routes 
})

const app = createApp(TaskManager) 
    .use(router) 
    .component('navigation', TaskManager)
    .mount('#app'); 
