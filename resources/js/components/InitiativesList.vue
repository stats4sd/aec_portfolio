<template>

    <!-- filters -->
    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <b class="mr-12"><i class="la la-filter"></i> Filter By Status</b>
            <b class="mr-8">Sort By:</b>
            <v-select
                v-model="sortBy"
                :options="sortOptions"
                :reduce="(option) => option.id"
            >
            </v-select>
            <h3 class="my-0 ml-4 btn btn-outline-info" @click="sortDir = -sortDir">
            <i
                class="la"
                :class="sortDir === 1 ? 'la-arrow-up' : 'la-arrow-down'"
                ></i>
            </h3>
        </div>

        <div>
            <span>Showing 1 to 8 of 8 entries</span>
            <button class="btn btn-link">Reset</button>
        </div>
    </div>


    <div v-for="initiative in initiatives">
        <h3>{{ initiative.name }}</h3>
    </div>
</template>

<script setup>
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';


import {ref, watch} from "vue";


const props = defineProps({
    initiatives: Object,
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
