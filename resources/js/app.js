import axios from 'axios';
import _ from 'lodash';
import {createApp} from 'vue/dist/vue.esm-bundler';

window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import InitiativesList from "./components/InitiativesList.vue"


createApp()
    .component('InitiativesList', InitiativesList)
    .mount('#initiativesListPage')
