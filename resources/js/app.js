import axios from 'axios';
import _ from 'lodash';
import {createApp} from 'vue';

import 'bootstrap';

import AgroecologicalPrinciplesAssessment from "./components/AgroecologicalPrinciplesAssessment.vue"

window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const app = createApp(AgroecologicalPrinciplesAssessment)
    .mount('#aePrinciplesAssessment')
