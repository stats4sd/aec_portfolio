import {createApp} from 'vue/dist/vue.esm-bundler';
import {Suspense} from "vue";

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

import {createVuetify} from "vuetify";
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
    components,
    directives,
})


import InitiativesList from "./components/InitiativesList.vue"
import Dashboard from "./components/Dashboard.vue";
import MainDashboard from "./components/MainDashboard.vue";


createApp()
    .use(vuetify)
    .component('dashboard', Dashboard)
    .component('Suspense', Suspense)
    .component('main-dashboard', MainDashboard)
    .mount('#dashboard');
