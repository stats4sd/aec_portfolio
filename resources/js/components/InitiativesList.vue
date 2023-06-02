<template>

    <!-- filters -->
    <div class="d-flex justify-content-between">
        <div>
            <b class="mr-12"><i class="la la-filter"></i> Filter By Status</b>
            <b>Sort By:</b>
            <v-select
                v-model="sortOrder"
                :options="sortOptions"
                :reduce="(option) => option.id"
            >

            </v-select>
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
])

const sortOrder = ref('name');


const propComparator = (propName) =>
  (a, b) => a[propName] === b[propName] ? 0 : a[propName] < b[propName] ? -1 : 1



watch(sortOrder, (newSortOrder) => {
    props.initiatives.sort(propComparator(newSortOrder))
})
</script>
