import axios from 'axios';
import _ from 'lodash';
import {createApp} from 'vue/dist/vue.esm-bundler';

import {Suspense} from "vue";
import 'vuetify/styles'
import {createVuetify} from "vuetify";
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components,
  directives,
})

window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import AgroecologicalPrinciplesAssessment from "./components/AgroecologicalPrinciplesAssessment.vue"

const app = createApp()
    .use(vuetify)
    .component('AgroecologicalPrinciplesAssessment', AgroecologicalPrinciplesAssessment)
    .component('Suspense', Suspense)
    .mount('#aePrinciplesAssessment')
