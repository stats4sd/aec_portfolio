import {createApp} from "vue/dist/vue.esm-bundler";

import UserFeedbackForm from "./components/UserFeedbackForm.vue";
import {Suspense} from "vue";


createApp()
    .component('UserFeedbackForm', UserFeedbackForm)
    .component('Suspense', Suspense)
    .mount('#user-feedback-form');
