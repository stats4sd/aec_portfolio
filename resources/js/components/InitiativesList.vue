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


    <InitiativeListCard v-for="initiative in initiatives" :key="initiative.id" :initiative="initiative" :has-additional-assessment="hasAdditionalAssessment"/>

</template>

<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'

import {ref, watch} from "vue";
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


// collapsable

</script>
