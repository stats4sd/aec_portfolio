<template>

    <!-- filters -->
    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center mb-8">
            <b class="mr-12"><i class="la la-filter"></i> Filter By Status</b>

            <b class="mr-8">Sort By:</b>
            <v-select
                style="width: 150px"
                v-model="sortBy"
                :options="sortOptions"
                :reduce="(option) => option.id"
                :clearable="false"
            >
            </v-select>

            <h3 class="my-0 ml-4 btn btn-outline-info" @click="sortDir = -sortDir">
                <i
                    class="la"
                    :class="sortDir === 1 ? 'la-arrow-up' : 'la-arrow-down'"
                ></i> Sort {{ sortDir === 1 ? 'Ascending' : 'Descending' }}
            </h3>
        </div>

        <div>
            <span>Showing 1 to 8 of 8 entries</span>
            <button class="btn btn-link">Reset</button>
        </div>
    </div>


    <div v-for="initiative in initiatives">
        <div class="card">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-12 col-lg-4">
                        <h4 class="font-weight-bold text-deep-green">{{ initiative.name.toUpperCase() }}</h4>
                    </div>
                    <div class="col-12"
                         :class="hasAdditionalAssessment ? 'col-lg-4' : 'col-lg-8'"
                    >
                        <h5 class="font-weight-bold text-bright-green">Current Assessment</h5>
                    </div>
                    <div v-if="hasAdditionalAssessment" class="col-12 col-lg-4">
                        <h5>Additional Assessment</h5>

                    </div>
                </div>

                <div class="row">
                    <div
                        class="col-12 col-lg-4 border border-top-0 border-left-0 border-bottom-0"
                    >
                        <div class="d-flex mb-3">
                            <div class="w-50">
                                <span class="font-weight-bold text-grey">CODE</span><br/>
                                <span class="font-weight-bold">{{ initiative.code }}</span>
                            </div>
                            <div>
                                <span class="font-weight-bold text-grey">PORTFOLIO</span><br/>
                                <span class="font-weight-bold">{{ initiative.portfolio.name }}</span>
                            </div>
                        </div>

                        <div class="btn btn-sm btn-primary">Edit Initiative Details</div>


                    </div>

                    <div class="col-12 d-flex flex-column justify-content-between"
                         :class="hasAdditionalAssessment ? 'col-lg-4' : 'col-lg-8'"
                    >
                        <div class="d-flex justify-content-between mb-3">
                            <div class="w-50">
                                <span class="font-weight-bold text-grey">STATUS</span><br/>
                                <span class="font-weight-bold">{{ initiative.latest_assessment.assessment_status }}</span>
                            </div>
                            <div class="w-50">
                                <span class="font-weight-bold text-grey">SCORE</span><br/>
                                <span class="font-xl text-bright-green font-weight-bold">{{ initiative.latest_assessment.overall_score }}%</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="w-50">
                                <span class="font-weight-bold text-grey">REDLINES</span><br/>
                                <span class="font-weight-bold">{{ initiative.latest_assessment.assessment_status }}</span>
                            </div>
                            <div class="w-50">
                                <div class="btn btn-primary" style="width: 140px">Assess Redlines</div>
                            </div>
                        </div>


                        <div class="d-flex justify-content-between mb-3">
                            <div class="w-50">
                                <span class="font-weight-bold text-grey">PRINCIPLES</span><br/>
                                <span class="font-weight-bold">{{ initiative.latest_assessment.assessment_status }}</span>
                            </div>
                            <div class="w-50">
                                <div class="btn btn-primary" style="width: 140px">Assess Principles</div>
                            </div>
                        </div>


                    </div>

                    <div v-if='hasAdditionalAssessment'
                         class="col-12 col-lg-4"
                    >

                    </div>

                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';


import {ref, watch} from "vue";


const props = defineProps({
    initiatives: Object,
    hasAdditionalAssessment: Boolean,
});

// Sorting
const sortOptions = ref([
    {
        id: 'created_at',
        label: 'Date added',
    },
    {
        id: 'name',
        label: 'Name',
    },
    {
        id: 'budget',
        label: 'Budget',
    }
])

const sortBy = ref('name')
const sortDir = ref(1)

// enable sorting on any property
const propComparator = (propName, sortDir) =>
    (a, b) => a[propName] === b[propName] ? 0 : a[propName] < b[propName] ? -sortDir : sortDir

watch(sortDir, (newSortDir) => {
    props.initiatives.sort(propComparator(sortBy.value, newSortDir))
})

watch(sortBy, (newSortBy) => {
    console.log('hi');
    props.initiatives.sort(propComparator(newSortBy, sortDir.value))
})
</script>
