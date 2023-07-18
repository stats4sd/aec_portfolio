import {createApp} from 'vue/dist/vue.esm-bundler';

import InstitutionSettings from "./components/InstitutionSettings.vue"
import {Suspense} from "vue";

createApp()
    .component('InstitutionSettings', InstitutionSettings)
    .component('Suspense', Suspense)
    .mount('#org-settings')
