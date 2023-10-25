import {createApp} from 'vue/dist/vue.esm-bundler';

import Dashboard from "./components/Dashboard.vue";


createApp()
    .component('dashboard', Dashboard)
    .mount('#dashboard');
