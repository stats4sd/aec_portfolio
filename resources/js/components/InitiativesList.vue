<template>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="font-weight-bold text-deep-green my-0">
            {{ organisation.name }} - Initiatives
        </h1>
    </div>
    <!-- filters -->
    <div class="d-flex justify-content-between flex-column flex-lg-row">
        <div class="d-flex flex-column mb-0 w-100">
            <div class="d-flex align-items-center justify-content-start">

                <div class="d-flex align-items-center">
                    <b class="mr-8 text-right" style="width: 30px;">Sort:</b>
                    <v-select
                        style="width: 150px"
                        v-model="sortBy"
                        :options="sortOptions"
                        :reduce="(option) => option.id"
                        :clearable="false"
                    >
                    </v-select>

                    <h3 class="my-0 mx-4 btn btn-outline-info" @click="sortDir = -sortDir">
                        <i
                            class="la"
                            :class="sortDir === 1 ? 'la-arrow-up' : 'la-arrow-down'"
                        ></i> Sort {{ sortDir === 1 ? 'Ascending' : 'Descending' }}
                    </h3>

                </div>

                <div class="flex-grow-1 justify-content-end d-flex">
                    <a v-if="showAddButton" href="/admin/project/create" class="btn btn-primary mr-2 ml-auto">Add Initiative</a>
                    <a v-if="showImportButton" href="/admin/project/import" class="btn btn-success mr-2">Import Initiatives</a>
                    <a v-if="showExportButton" href="/admin/organisation/export" class="btn btn-info">Export All Initiative Data</a>
                </div>
            </div>
            <div class="d-flex align-items-center flex-column flex-md-row mt-4">
                <b class="mr-8" style="width: 30px;">Filters:</b>
                <v-select
                    style="width: 250px"
                    class="mr-4 mb-3 mb-md-0"
                    v-model="redlineStatusFilter"
                    :options="makeFilterOptions('Red Flags')"
                    placeholder="Filter By Red Flag Status"
                    :clearable="true"
                />

                <v-select
                    style="width: 250px"
                    class="mr-4"
                    v-model="principleStatusFilter"
                    :options="makeFilterOptions('Principles')"
                    placeholder="Filter By Assessment Status"
                    :clearable="true"
                />

                <v-select
                    style="width: 350px"
                    class="mr-4"
                    v-model="portfolioFilter"
                    :options="portfolios"
                    placeholder="Filter By Portfolio"
                    :clearable="true"
                />

                <div class="flex-grow-1">
                    <input style="min-width: 250px" class="form-control" placeholder="Search Initiatives By Name" v-model="searchString"/>
                </div>

            </div>
            <div class="d-flex justify-content-end align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <div class="font-sm">Showing 1 to {{ filteredInitiatives.length }} of {{ filteredInitiatives.length }} entries</div>
                    <button class="btn btn-link" @click="resetFilters">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <InitiativeListCard v-for="initiative in filteredInitiatives" :key="initiative.id" :initiative="initiative" :has-additional-assessment="hasAdditionalAssessment" :enable-edit-button="enableEditButton" :enable-show-button="enableShowButton" :enable-assess-button="enableAssessButton"
    />

</template>

<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'

import {computed, ref, watch, onMounted} from "vue";
import InitiativeListCard from "./InitiativeListCard.vue";


const props = defineProps({
    organisation: Object,
    initiatives: Object,
    hasAdditionalAssessment: Boolean,
    showAddButton: Boolean,
    showImportButton: Boolean,
    showExportButton: Boolean,
    enableEditButton: Boolean,
    enableShowButton: Boolean,
    enableAssessButton: Boolean,
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

// default value for sortBy and sortDir, which is sorted by name in ascending order
const sortBy = ref('name')
const sortDir = ref(1)

// enable sorting on any property
const propComparator = (propName, sortDir) =>
    (a, b) => a[propName] === b[propName] ? 0 : a[propName] < b[propName] ? -sortDir : sortDir

watch(sortDir, (newSortDir) => {
    props.initiatives.sort(propComparator(sortBy.value, newSortDir))
})

watch(sortBy, (newSortBy) => {
    props.initiatives.sort(propComparator(newSortBy, sortDir.value))
})


// status filter
const redlineStatusFilter = ref('');
const principleStatusFilter = ref('');
const portfolioFilter = ref('');

// keyword search filter
const searchString = ref('')

const portfolios = computed(() => {
    return props.initiatives.map(initiative => initiative.portfolio.name)
        .reduce((cumulative, current) => {
            if (cumulative.includes(current)) {
                return cumulative;
            }
            cumulative.push(current)
            return cumulative
        }, [])
})

const filterOptions = ref([
    {
        label: 'Not Started',
        value: 'Not Started',
    },
    {
        label: 'In Progress',
        value: 'In Progress',
    },
    {
        label: 'Complete',
        value: 'Complete',
    },
    {
        label: 'Failed',
        value: 'Failed',
    },
])

function makeFilterOptions(string) {
    return filterOptions.value.map(option => {
        return {
            value: option.value,
            label: string + ' - ' + option.label,
        };
    })
}

const filteredInitiatives = computed(() => {
    // just mentioning variables, computed() function will be triggered when their value changed
    sortBy.value;
    sortDir.value;

    // declare temporary variables for filtering
    let tempInitiatives;
    tempInitiatives = props.initiatives;

    // apply filter for red flag status
    if (redlineStatusFilter.value) {
        tempInitiatives = tempInitiatives.filter(
            initiative => initiative.latest_assessment.redline_status === redlineStatusFilter.value.value
        )
    }

    // apply filter for principle status
    if (principleStatusFilter.value) {
        tempInitiatives = tempInitiatives.filter(
            initiative => initiative.latest_assessment.principle_status === principleStatusFilter.value.value
        )
    }

    // apply filter for portfolio
    if (portfolioFilter.value) {
        tempInitiatives = tempInitiatives.filter(
            initiative => initiative.portfolio.name === portfolioFilter.value
        )
    }

    // apply filter for keyword
    if (searchString.value !== '') {
        tempInitiatives = tempInitiatives.filter(
            initiative => initiative.name.toLowerCase().includes(searchString.value.toLowerCase())
        )
    }

    return tempInitiatives;
})

function resetFilters() {
    redlineStatusFilter.value = '';
    principleStatusFilter.value = '';
    portfolioFilter.value = '';
    searchString.value = '';

    handlePortfolioFromUrl();
}

// handle portfolio from url
function handlePortfolioFromUrl() {
    const querypairs = window.location.search.substring(1);
    const test = new URLSearchParams(querypairs)
    console.log(test)

    test.forEach((value, key) => {
        if (key === 'portfolio') {
            portfolioFilter.value = value;
        }
    })
}


onMounted(() => {
    handlePortfolioFromUrl();
})


</script>
