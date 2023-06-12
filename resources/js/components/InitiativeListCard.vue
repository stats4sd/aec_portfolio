<template>
    <div class="card">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-12 col-lg-4 border border-top-0 border-left-0 border-bottom-0">
                    <h4 class="font-weight-bold text-deep-green">{{ initiative.name.toUpperCase() }}</h4>
                </div>
                <div
                    class="col-12"
                    :class="hasAdditionalAssessment ? 'col-lg-3' : 'col-lg-5'"
                >
                    <h5 class="font-weight-bold text-bright-green">Current Assessment</h5>
                    <div class="d-flex justify-content-between">
                        <div class="w-50">
                            <span class="font-weight-bold text-grey">STATUS</span><br/>
                            <span class="font-weight-bold">{{ initiative.latest_assessment.assessment_status }}</span>
                        </div>
                        <div class="w-50">
                            <span class="font-weight-bold text-grey">SCORE</span><br/>
                            <span class="font-xl text-bright-green font-weight-bold" v-if="initiative.latest_assessment.overall_score">{{ initiative.latest_assessment.overall_score }}%</span>
                        </div>
                    </div>
                </div>
                <div v-if="hasAdditionalAssessment" class="col-12 col-lg-3">
                    <h5>Additional Assessment</h5>
                </div>
                <div class="col-12 col-lg-3 d-flex align-items-center justify-content-end">
                    <a :href='nextAction.url' class="btn btn-success mr-2">
                        Next Step
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
                            <div class="d-flex">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">CODE</span><br/>
                                    <span class="font-weight-bold">{{ initiative.code }}</span>
                                </div>
                                <div>
                                    <span class="font-weight-bold text-grey">PORTFOLIO</span><br/>
                                    <span class="font-weight-bold">{{ initiative.portfolio.name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12"
                             :class="hasAdditionalAssessment ? 'col-lg-4' : 'col-lg-6'">
                            <div class="d-flex justify-content-between mt-3">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">REDLINES</span><br/>
                                    <span class="font-weight-bold">{{ initiative.latest_assessment.redline_status }}</span>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/redline`"
                                        class="btn text-light"
                                        :class="initiative.latest_assessment.redline_status === 'Complete' ? 'btn-secondary' : 'btn-success'"
                                        style="width: 140px"
                                    >
                                        Assess Redlines
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <div class="w-50">
                                    <span class="font-weight-bold text-grey">PRINCIPLES</span><br/>
                                    <span class="font-weight-bold">{{ initiative.latest_assessment.principle_status }}</span>
                                </div>
                                <div class="w-50">
                                    <a
                                        :href="`/admin/assessment/${initiative.latest_assessment.id}/assess`"
                                        class="btn"
                                        :class="initiative.latest_assessment.redline_status === 'Complete' ? (initiative.latest_assessment.principle_status === 'Complete' ? 'btn-secondary' : 'btn-success') : 'btn-light disabled'"
                                        style="width: 140px"
                                    >
                                        Assess Principles
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

    if(redlineStatus !== "Complete") {
        return {
            label: "Assess Redlines",
            url: `/admin/assessment/${props.initiative.latest_assessment.id}/redline`
        }
    }

    if(principleStatus !== "Complete") {
        return {
            label: "Assess Principles",
            url: `/admin/assessment/${props.initiative.latest_assessment.id}/assess`
        }
    }

    if(props.hasAdditionalAssessment &&  additionalStatus !== "Complete") {
        return {
            label: "Assess Additional Criteria",
            url: `/admin/assessment/${props.initiative.latest_assessment.id}/assess-custom`
        }
    }

    return {
        label: "---",
        url: "#",
    }

})


</script>
