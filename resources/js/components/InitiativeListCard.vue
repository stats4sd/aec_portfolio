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
                            <span class="font-weight-bold text-grey">STATUS</span><br/>
                            <span class="font-weight-bold">{{ initiative.latest_assessment.assessment_status }}</span>
                        </div>
                        <div class="w-50">
                            <span class="font-weight-bold text-grey">SCORE</span><br/>
                            <span class="font-xl text-bright-green font-weight-bold" v-if="initiative.latest_assessment.overall_score !== null">{{ initiative.latest_assessment.overall_score }}%</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 d-flex align-items-center justify-content-end">
                    <a :href='nextAction.url' class="btn btn-success mr-2" v-if="nextAction">
                        {{ nextAction.label }}
                    </a>
                    <div class="btn btn-info" @click="toggleExpand" data-toggle="collapse" :data-target="'.initiative-collapse_'+initiative.id">
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
                            <a class="btn btn-primary" :href="`/admin/project/${initiative.id}/edit`">Edit Initiative Details</a>
                            <a class="btn btn-success" :href="`/admin/project/${initiative.id}/show`">Show Initiative Information</a>

                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="d-flex justify-content-between mt-3 w-100">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">RED FLAGS</span><br/>
                                    <span class="font-weight-bold">{{ initiative.latest_assessment.redline_status }}</span>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/redline`"
                                        class="btn text-light w-100"
                                        :class="initiative.latest_assessment.redline_status === 'Complete' ? 'btn-info' : 'btn-success'"
                                    >
                                        {{ initiative.latest_assessment.redline_status === 'Complete' ? 'Edit Red Flag Assessment' : 'Assess Red Flags' }}
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3 w-100">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">PRINCIPLES</span><br/>
                                    <span class="font-weight-bold">{{ initiative.latest_assessment.principle_status }}</span>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/assess`"
                                        class="btn text-light w-100"
                                        :class="initiative.latest_assessment.redline_status === 'Complete' ? (initiative.latest_assessment.principle_status === 'Complete' ? 'btn-info' : 'btn-success') : 'btn-info disabled'"

                                    >
                                        {{ initiative.latest_assessment.principle_status === 'Complete' ? 'Edit Principle Assessment' : 'Assess Red Flags' }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="hasAdditionalAssessment" class="d-flex justify-content-between mt-3 w-100">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">ADDITIONAL CRITERIA</span><br/>
                                    <span class="font-weight-bold">{{ initiative.latest_assessment.additional_status }}</span>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/assess-custom`"
                                        class="btn text-light w-100"
                                        :class="initiative.latest_assessment.redline_status === 'Complete' ? (initiative.latest_assessment.additional_status === 'Complete' ? 'btn-info' : 'btn-success') : 'btn-info disabled'"

                                    >
                                        {{ initiative.latest_assessment.additional_status === 'Complete' ? 'Edit Additional Criteria Assessment' : 'Assess Additional Criteria' }}
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

import {computed, ref} from "vue";

const props = defineProps({
    initiative: Object,
    hasAdditionalAssessment: Boolean,
})

const expanded = ref(false)

function toggleExpand() {
    expanded.value = !expanded.value
}

const nextAction = computed(() => {

    const redlineStatus = props.initiative.latest_assessment.redline_status
    const principleStatus = props.initiative.latest_assessment.principle_status
    const additionalStatus = props.initiative.latest_assessment.additional_status

    if (redlineStatus === "Failed") {
        return null;
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
        label: "Show Initiative Information",
        url: `/admin/project/${props.initiative.id}/show`,
    }

})


</script>
