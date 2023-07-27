<template>
    <div class="card-header">
        <h2>User Feedback</h2>
        <p class="font-lg">Share feedback with the Site Management team</p>
    </div>
    <div class="card-body font-lg">
        <p>If you encounter any technical issues while using the tool, please let us know here. You can contact us using the form below.</p>


        <div class="card border border-info mt-4">
            <div class="card-header">
                <h3>Submit Feedback</h3>
            </div>
            <div class="card-body">
                <div class="form-group row mt-8">
                    <label for="input_hq_country" class="col-sm-4 col-form-label text-right pr-2">What type of feedback do you want to submit?</label>
                    <div class="col-sm-8 col-lg-4">
                        <v-select
                            name="user_feedback_type"
                            id="input_type"
                            :options="userFeedbackTypes"
                            label="name"
                            :reduce="type => type.id"
                            v-model="form.user_feedback_type_id"
                        />
                        <span class="text-danger emphasis show" role="alert" v-if="errors.hasOwnProperty('user_feedback_type_id')">
                            <strong v-for="error in errors.user_feedback_type_id">{{ error }}</strong><br/>
                        </span>
                    </div>
                </div>
                <div class="form-group row mt-8">
                    <label for="input_hq_country" class="col-sm-4 col-form-label text-right pr-2">Enter your message</label>

                    <div class="col-sm-8">
                    <textarea
                        name="message"
                        id="input_message"
                        v-model="form.message"
                        class="w-100"
                        rows="7"
                    />
                        <span class="text-danger emphasis show" role="alert" v-if="errors.hasOwnProperty('message')">
                            <strong v-for="error in errors.message">{{ error }}</strong><br/>
                        </span>
                    </div>
                </div>

                <div class="form-group row mt-8">
                    <label for="input_hq_country" class="col-sm-4 col-form-label text-right pr-2">If you have any screenshots to support your feedback, please upload them here</label>

                    <div class="col-sm-8">
                        <media-library-collection name="uploads" @change="changeMedia" ref="mediaCollectionComponent"/>
                        <span class="text-danger emphasis show" role="alert" v-if="errors.hasOwnProperty('message')">
                            <strong v-for="error in errors.message">{{ error }}</strong><br/>
                        </span>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-8 offset-sm-4">
                        <div class="form-check">
                            <input
                                id="input_include_details"
                                name="input_include_details"
                                class="form-check-input"
                                type="checkbox"
                                v-model="includeDetails"
                            >
                            <label class="form-check-label" for="input_include_details">
                                Do you want to include your user details along with the feedback?<br/><br/>
                                <span class="font-md">This is optional - you may submit feedback anonymously if you wish. If you do share your details, your user account will be associated with this feedback. This will allow us to followup with you for further information, or reach out to provide support if possible.</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex justify-content-end mt-0"
                >
                    <button type="cancel" class="btn btn-secondary mr-4">Cancel</button>
                    <button class="btn btn-primary" @click="save">Submit</button>
                </div>

            </div>
        </div>

    </div>
</template>

<script setup>

import {ref, watch} from "vue";
import 'vue-select/dist/vue-select.css'
import vSelect from "vue-select";
import Swal from "sweetalert2";

import {MediaLibraryCollection} from "spatie-media-library-pro/media-library-pro-vue3-collection";


const props = defineProps({
    user: Object,
    postRoute: String,
    userFeedbacks: Array,
    userFeedbackTypes: Array,
});

const errors = ref({})
const form = ref({})

async function save() {
    try {

        // merge main form with file uploads
        let uploadForm = form.value;
        uploadForm.uploads = uploads.value;

        await axios.post(props.postRoute, uploadForm);

        await Swal.fire({
            title: "Thank you for your Feedback",
            text: "Your message has been submitted, and will be reviewed by the Site Management team."
        });


        // reset form
        form.value = {}
        includeDetails.value = false;
        mediaCollectionComponent.value.mediaLibrary.state.media = [];



    } catch (error) {
        errors.value = error.response?.data.errors ?? {}
    }

}

// include details or not
const includeDetails = ref(false);
watch(includeDetails, (newValue) => {
    console.log(newValue);
    if (newValue) {
        form.value.user_id = props.user.id;
    } else {
        delete form.value.user_id;
    }
})


// track media
const uploads = ref({});
const mediaCollectionComponent = ref(null)
function changeMedia(media) {
    uploads.value = media;

    console.log(mediaCollectionComponent.value.mediaLibrary)
}

</script>
