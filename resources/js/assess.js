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

import AgroecologicalPrinciplesAssessment from "./components/AgroecologicalPrinciplesAssessment.vue"

createApp()
    .use(vuetify)
    .component('AgroecologicalPrinciplesAssessment', AgroecologicalPrinciplesAssessment)
    .component('Suspense', Suspense)
    .mount('#aePrinciplesAssessment')
