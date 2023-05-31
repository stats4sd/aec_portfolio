import axios from 'axios';
import _ from 'lodash';
import {createApp} from 'vue/dist/vue.esm-bundler';

import {Suspense} from "vue";

//import 'bootstrap';

window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import AgroecologicalPrinciplesAssessment from "./components/AgroecologicalPrinciplesAssessment.vue"

const app = createApp()
    .component('AgroecologicalPrinciplesAssessment', AgroecologicalPrinciplesAssessment)
    .component('Suspense', Suspense)
    .mount('#aePrinciplesAssessment')
