import {createApp} from 'vue/dist/vue.esm-bundler';

import InstitutionSettings from "./components/InstitutionSettings.vue"


createApp()
    .component('InstitutionSettings', InstitutionSettings)
    .mount('#org-settings')
