<template>
    <div class="card" style="min-width:325px;">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-12 col-lg-4 border border-top-0 border-left-0 border-bottom-0">
                    <h4 class="font-weight-bold text-deep-green">{{ initiative.name.toUpperCase() }}</h4>
                </div>
                <div
                    class="col-12 col-lg-5"
                >
                    <h5 class="font-weight-bold text-bright-green">Current Assessment</h5>
                    <div class="d-flex justify-content-between">
                        <div class="w-50">
                            <span class="font-weight-bold text-grey">STATUS</span>
                            <v-help-text-link location="Initiatives - statuses" type="popover" :help-text-entry-init="statusHelpText"/>
                            <br/>
                            <span class="font-weight-bold">{{ initiative.latest_assessment.assessment_status }}</span>
                        </div>
                        <div class="w-50">
                            <span class="font-weight-bold text-grey">AE SCORE</span>
                            <v-help-text-link location="Initiatives - score" type="popover" :help-text-entry-init="scoreHelpText"/>
                            <br/>
                            <span class="font-xl text-bright-green font-weight-bold"
                                  v-if="initiative.latest_assessment.overall_score !== null">{{
                                    initiative.latest_assessment.overall_score
                                }}%</span>

                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 d-flex align-items-center justify-content-end">
                    <a :href='nextAction.url' class="btn mr-2"
                       :class="enableAssessButton ? 'btn-success' : 'btn-info disabled'" v-if="nextAction">
                        {{ nextAction.label }}
                    </a>
                    <div class="btn btn-info" @click="toggleExpand(initiative.id)" data-toggle="collapse"
                         :data-target="'.initiative-collapse_'+initiative.id">
                        <i class="la"
                           :class="expanded ? 'la-caret-down' : 'la-caret-right'"></i>
                    </div>
                </div>
            </div>
            <Transition>

                <div v-if="expanded">

                    <div class="row">
                        <div class="col-12 col-lg-4 border border-bottom-0 border-left-0 border-top-0">
                            <div class="d-flex mb-4">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">CODE</span><br/>
                                    <span class="font-weight-bold">{{ initiative.code }}</span>
                                </div>
                                <div>
                                    <span class="font-weight-bold text-grey">PORTFOLIO</span><br/>
                                    <span class="font-weight-bold">{{ initiative.portfolio.name }}</span>
                                </div>
                            </div>

                            <a class="btn btn-primary mr-2 mb-2" :class="[ enableEditButton ? '' : 'disabled']"
                               :href="`/admin/project/${initiative.id}/edit`">Edit Details</a>

                            <a class="btn btn-success mr-2 mb-2" :class="[ enableShowButton ? '' : 'disabled']"
                               :href="`/admin/project/${initiative.id}/show`">Show Information</a>

                            <button class="btn btn-danger mr-2 mb-2" :class="[ enableDeleteButton ? '' : 'disabled']"
                                    @click="enableDeleteButton ? removeInitiative() : '';">Delete
                            </button>

                            <button class="btn btn-warning mr-2 mb-2" :class="[ enableReassessButton ? (initiative.latest_assessment.principle_status === 'Complete' && initiative.latest_assessment.additional_status != 'Not Started' ? '' : 'disabled') : 'disabled']"
                                    @click="enableReassessButton ? (initiative.latest_assessment.principle_status === 'Complete' && initiative.latest_assessment.additional_status != 'Not Started' ? reassessInitiative() : 'disabled') : '';">Reassess
                            </button>

                            <button class="btn btn-warning mr-2 mb-2" :class="[ enableDeleteButton ? '' : 'disabled']"
                               @click="enableDeleteButton ? duplicateInitiative() : '';">Duplicate</button>

                        </div>

                        <div class="col-12 col-lg-7">
                            <hr/>
                            <div class="d-flex justify-content-between mt-3 w-100">
                                <div class="w-50 d-flex justify-content-between">
                                    <div>

                                        <span class="font-weight-bold text-grey">RED FLAGS</span><br/>
                                        <span class="font-weight-bold">{{
                                                initiative.latest_assessment.redline_status
                                            }}</span>
                                    </div>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/redline`"
                                        class="btn text-light w-100"
                                        :class="enableAssessButton ? (initiative.latest_assessment.redline_status === 'Complete' ? 'btn-info' : 'btn-success') : 'btn-info disabled'"

                                    >
                                        {{
                                            initiative.latest_assessment.redline_status === 'Complete' ? 'Edit Red Flag Assessment' : 'Assess Red Flags'
                                        }}
                                    </a>
                                </div>
                            </div>
                            <hr/>
                            <div class="d-flex justify-content-between mt-3 w-100">
                                <div class="w-50 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold text-grey">PRINCIPLES</span><br/>
                                        <span class="font-weight-bold">{{
                                                initiative.latest_assessment.principle_status
                                            }}</span>
                                    </div>
                                    <div class="pr-12 py-auto">
                                        <span v-if="initiative.latest_assessment.overall_score !== null" class="font-lg text-bright-green font-weight-bold">
                                            {{ initiative.latest_assessment.overall_score }} %
                                        </span>
                                    </div>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/assess`"
                                        class="btn text-light w-100"
                                        :class="enableAssessButton ? (initiative.latest_assessment.redline_status === 'Complete' ? (initiative.latest_assessment.principle_status === 'Complete' ? 'btn-info' : 'btn-success') : 'btn-info disabled') : 'btn-info disabled'"

                                    >
                                        {{
                                            initiative.latest_assessment.principle_status === 'Complete' ? 'Edit Principle Assessment' : 'Assess Principles'
                                        }}
                                    </a>
                                </div>
                            </div>
                            <hr/>
                            <div v-if="hasAdditionalAssessment" class="d-flex justify-content-between mt-3 w-100">
                                <div class="w-50 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold text-grey">ADDITIONAL CRITERIA</span><br/>
                                        <span class="font-weight-bold">{{
                                                initiative.latest_assessment.additional_status
                                            }}</span>
                                    </div>
                                    <div class="pr-12 py-auto">
                                        <span v-if="initiative.latest_assessment.additional_score !== null" class="font-lg text-bright-green font-weight-bold">
                                            {{ initiative.latest_assessment.additional_score }} %
                                        </span>
                                    </div>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/assess-custom`"
                                        class="btn text-light w-100"
                                        :class="enableAssessButton ? (initiative.latest_assessment.redline_status === 'Complete' ? (initiative.latest_assessment.additional_status === 'Complete' ? 'btn-info' : 'btn-success') : 'btn-info disabled') : 'btn-info disabled'"

                                    >
                                        {{
                                            initiative.latest_assessment.additional_status === 'Complete' ? 'Edit Additional Criteria Assessment' : 'Assess Additional Criteria'
                                        }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>
<script setup>

import {computed, defineEmits, ref} from "vue";
import Swal from "sweetalert2";
import VHelpTextLink from "./vHelpTextLink.vue";

const props = defineProps({
    initiative: Object,
    hasAdditionalAssessment: Boolean,
    enableEditButton: Boolean,
    enableShowButton: Boolean,
    enableAssessButton: Boolean,
    enableDeleteButton: Boolean,
    enableReassessButton: Boolean,
    statusHelpText: Object,
    scoreHelpText: Object,
    expandedStart: Boolean
})

const expanded = ref(props.expandedStart)

const emit = defineEmits(['remove_initiative'])

function toggleExpand(){
    // alert('toggleExpand');

    expanded.value = !expanded.value

    let result = axios.post('/store-project-id-in-session', {
        projectId: props.initiative.id,
        expanded: expanded.value
    });

}

const nextAction = computed(() => {

    const redlineStatus = props.initiative.latest_assessment.redline_status
    const principleStatus = props.initiative.latest_assessment.principle_status
    const additionalStatus = props.initiative.latest_assessment.additional_status

    if (redlineStatus === "Failed") {
        return {
            label: "Show information",
            url: `/admin/project/${props.initiative.id}/show`,
        }
    }

    if (redlineStatus !== "Complete") {
        return {
            label: "Assess Red Flags",
            url: `/admin/assessment/${props.initiative.latest_assessment.id}/redline`
        }
    }

    if (principleStatus !== "Complete") {
        return {
            label: "Assess Principles",
            url: `/admin/assessment/${props.initiative.latest_assessment.id}/assess`
        }
    }

    if (props.hasAdditionalAssessment && additionalStatus !== "Complete") {
        return {
            label: "Assess Additional Criteria",
            url: `/admin/assessment/${props.initiative.latest_assessment.id}/assess-custom`
        }
    }

    return {
        label: "Show information",
        url: `/admin/project/${props.initiative.id}/show`,
    }

})


async function removeInitiative() {
    let choice = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    });

    if (choice.isConfirmed) {
        // remove initiative via ajax request
        let result = await axios.delete(`/admin/project/${props.initiative.id}`);


        new Noty({
            type: "success",
            text: `You have successfully deleted the initiative ${props.initiative.name}`,
        }).show();

        emit('remove_initiative', props.initiative)
    }

}

async function reassessInitiative() {
    let choice = await Swal.fire({
        title: 'Are you sure?',
        text: "The current assessment will be archived and the initiative will be reset to the beginning, allowing you to complete the assessment process again based on the current status of the initiative.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reassess!'
    });

    if (choice.isConfirmed) {

        let result = await axios.post(`/admin/project/${props.initiative.id}/reassess`);

        Swal.fire(
            'Archived!',
            'The current assessment has been archived. The initiative can now be reassessed.',
            'success'
        );

        // pass the refreshed initiative out to the list
        emit('refresh_initiative', result.data)

    }
}

async function duplicateInitiative() {
    let choice = await Swal.fire({
        title: "Are you sure?",
        text: "This initiative will be duplicated, along with any associated assessments, complete or in progress. You will be taken to the edit page for the new initiative.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, duplicate it!",
    });

    if(choice.isConfirmed) {
        let result = await axios.post(`/admin/project/${props.initiative.id}/duplicate`);

        new Noty({
            type: "success",
            text: `You have successfully duplicated the initiative ${props.initiative.name}`,
        }).show();

        window.location.href = `/admin/project/${result.data.id}/edit`;
    }
}


</script>
