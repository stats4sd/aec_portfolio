import {createApp} from 'vue/dist/vue.esm-bundler';

import InitiativesList from "./components/InitiativesList.vue"

createApp()
    .component('InitiativesList', InitiativesList)
    .mount('#initiativesListPage')
