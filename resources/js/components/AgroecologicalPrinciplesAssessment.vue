<template>
    <div class="card w-100 px-4 py-4 my-1">
        <div class="card-body">
            <div class="px-5 mx-5">
                <h3 class="text-bright-green font-weight-bold">Agrocological Principles Assessment</h3>
                <h3 class="text-deep-green font-weight-bold">{{ assessment.project.name.toUpperCase() }}</h3>
                <div class="mb-5">


                    This is the main section of the review. Below are the 13 Agroecology Principles, and you should rate
                    the project against each one.
                    <br/><br/>
                    For each principle, you should give:

                    <ul>
                        <li>A rating: This is a number between 0 and 2, based on your appreciation of the value of the
                            principle in the project design / activities, and following the Spectrum defined for each
                            principle. Decimal digits are allowed.
                        </li>
                        <li>A comment: Please add any comments to help explain the rating, and about how the principle
                            is seen within the project.
                        </li>
                    </ul>
                    Each principle also lists a set of example activities relevant to that principle. Please tick all
                    activities that are present in the project.
                    <br/><br/>
                    <b class="text-deep-green">Click on a principle to begin:</b>
                </div>

                <div class="row">
                    <div class="col-6" v-for="principleAssessment in principleAssessments">
                        <div
                            class="card rounded-pill"
                            :class="principleAssessment.complete ? 'bg-deep-green' : 'bg-light'"
                            @click="selectedPrincipleAssessment = principleAssessment; modalIsOpen = true"
                        >
                            <button class="card-body py-3 btn btn-light rounded-pill"
                                    :class="principleAssessment.complete ? 'bg-white' : ''"
                            >
                                <div class="px-4 d-flex justify-content-between align-items-center"
                                     :class="principleAssessment.complete ? 'text-deep-green' : ''"
                                >
                                    <span>{{ principleAssessment.principle.name }}</span>
                                    <div class="d-flex align-items-end">
                                        <h5 class="py-0 m-0 pr-4" style="line-height: 1.3em;"
                                            v-if="principleAssessment.complete">
                                            {{
                                                principleAssessment.is_na ? 'N/A' : Math.round(principleAssessment.rating * 10) / 10
                                            }}
                                        </h5>
                                        <h3 class="p-0 m-0 d-flex">
                                            <i :class="principleAssessment.complete ? 'la la-check-circle' : 'la la-edit'"></i>
                                        </h3>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <p class="ml-auto mr-auto">Once you have completed all {{ principleAssessments.length }} principles, you may
            mark the assessment as complete below.</p>
    </div>
    <div class="ml-auto mr-auto d-flex flex-column align-items-center mt-4">
        <v-checkbox
            label="I confirm this assessment is complete and correct to the best of my knowledge."
            density="compact"
            hide-details="always"
            :disabled="!allComplete"
            v-model="assessmentComplete"
        />

        <div class="d-flex justify-content-around">

            <form method="POST" :action="`/admin/${assessmentType}/${assessment.id}/finalise`" class="mt-4">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="_redirect" value="/admin/project">
                <button
                    class="btn"
                    :class="assessmentComplete ? 'btn-success' : 'btn-secondary'"
                    type="submit"
                    :disabled="!assessmentComplete"
                >
                    <span class="la la-save"></span> Finalise Assessment
                </button>
            </form>

            <form method="POST" :action="`/admin/${assessmentType}/${assessment.id}/finalise`" class="mt-4 ml-4"
                  v-if="assessment.has_additional_criteria">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="_redirect" :value="`/admin/assessment/${assessment.id}/assess-custom`">
                <button
                    class="btn"
                    :class="assessmentComplete ? 'btn-success' : 'btn-secondary'"
                    type="submit"
                    :disabled="!assessmentComplete"
                >
                    <span class="la la-arrow-right"></span>  Finalise and begin Additional Assessment
                </button>
            </form>
        </div>
    </div>


    <v-dialog
        v-model="modalIsOpen"
        width="80vw"
        max-width="1500px"
        :scrollable="true"
    >
        <PrincipleAssessmentModal
            v-if="selectedPrincipleAssessment"
            :principle-assessment="selectedPrincipleAssessment"
            :assessment-type="assessmentType"
            @discard="discard"
            @close="modalIsOpen = false"
            @next="next"
            @update_rating="updateRating"
        />

    </v-dialog>
</template>

<script setup>
import {onMounted, ref, computed} from "vue";


import PrincipleAssessmentModal from "./PrincipleAssessmentModal.vue";

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

const props = defineProps({
    assessmentType: String,
    assessment: Object,
})

async function getAssessments(url) {
    const res = await axios.get(url);
    return res.data
}

// get principle or additional Assessments
let url = `/assessment/${props.assessment.id}/principle-assessments/`

if (props.assessmentType === "additional") {
    url = `/assessment/${props.assessment.id}/additional-assessments`
}

const res = await getAssessments(url);

let principleAssessments = ref(res);

// select + edit principle assessments
let selectedPrincipleAssessment = ref({})
let modalIsOpen = ref(false)

async function discard() {
    principleAssessments.value = await getPrincipleAssessments()
    modalIsOpen.value = false;
}


function next() {

    const index = principleAssessments.value.indexOf(selectedPrincipleAssessment.value)

    // if we've reached the end...
    if (index + 1 >= principleAssessments.value.length) {
        modalIsOpen.value = false
        return
    }

    selectedPrincipleAssessment.value = principleAssessments.value[index + 1]

}

// handle completion
const allComplete = computed(() => principleAssessments.value.every(pa => pa.complete))
const assessmentComplete = ref(props.assessment.complete)


function updateRating(principleAssessment) {
    const index = principleAssessments.value.findIndex(item => item.id === principleAssessment.id)

    principleAssessments.value[index] = principleAssessment
}

</script>
