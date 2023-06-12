<template>

    <!-- filters -->
    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center mb-8">

            <v-select
                style="width: 250px"
                class="mr-4"
                v-model="statusFilter"
                :options="filterOptions"
                placeholder="Filter By Status"
                :clearable="true"

            >

            </v-select>
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
            <span>Showing 1 to {{ initiatives.length }} of {{ initiatives.length }} entries</span>
            <button class="btn btn-link">Reset</button>
        </div>
    </div>


    <InitiativeListCard v-for="initiative in filteredInitiatives" :key="initiative.id" :initiative="initiative" :has-additional-assessment="hasAdditionalAssessment"/>

</template>

<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'

import {computed, ref, watch} from "vue";
import InitiativeListCard from "./InitiativeListCard.vue";


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


// status filter
const statusFilter = ref('');
const filterOptions = ref([{
    label: 'Redlines: Not Started',
    filterItem: 'redlines',
    value: 'Not Started',
},
    {
        label: 'Redlines: In Progress',
        filterItem: 'redlines',
        value: 'In Progress',
    },
    {
        label: 'Redlines: Complete',
        filterItem: 'redlines',
        value: 'Complete',
    },
    {
        label: 'Principles: In Progress',
        filterItem: 'principles',
        value: 'In Progress',
    },
    {
        label: 'Principles: Complete',
        filterItem: 'principles',
        value: 'Complete',
    },
])

if (props.hasAdditionalAssessment) {
    filterOptions.push({
        label: "Additional Criteria: In Progress",
        filterItem: "additionalCriteria",
        value: 'In Progress',
    })
    filterOptions.push({
        label: "Additional Criteria: Complete",
        filterItem: "additionalCriteria",
        value: 'Complete',
    })
}

const filteredInitiatives = computed(() => {
    if(statusFilter.value) {
        return props.initiatives.filter(initiative => {
            if(statusFilter.value.filterItem === "redlines") {
                return initiative.latest_assessment.redline_status === statusFilter.value.value
            }

            if(statusFilter.value.filterItem === "principles") {
                return initiative.latest_assessment.assessment_status === statusFilter.value.value
            }

            if(statusFilter.value.filterItem === "redlines") {
                return initiative.latest_assessment.additional_status === statusFilter.value.value
            }
        })
    }

    return props.initiatives;
})

</script>
