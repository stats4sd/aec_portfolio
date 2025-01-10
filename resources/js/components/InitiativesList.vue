<template>

    <div class="d-flex justify-content-start align-items-center mb-4">
        <h1 class="font-weight-bold text-deep-green my-0">
            {{ organisation.name }} - Initiatives
        </h1>
        <v-help-text-link class="font-2xl" location="Initiatives - List page"/>
    </div>

    <v-help-text-entry class="mb-4" location="Initiatives - List page"/>

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
                            :class="sortDir == 1 ? 'la-arrow-up' : 'la-arrow-down'"
                        ></i> Sort {{ sortDir == 1 ? 'Ascending' : 'Descending' }}
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
                    :reduce="option => option.value"
                    placeholder="Filter By Red Flag Status"
                    :clearable="true"
                />

                <v-select
                    style="width: 250px"
                    class="mr-4"
                    v-model="principleStatusFilter"
                    :options="makeFilterOptions('Principles')"
                    :reduce="option => option.value"
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
                    <input style="min-width: 250px" class="form-control" placeholder="Search Initiatives By Name or Code" v-model="searchString"/>
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

    <InitiativeListCard
        v-for="initiative in filteredInitiatives"
        :id="initiative.id"
        :key="initiative.id"
        @remove_initiative="removeInitiative"
        @refresh_initiative="refreshInitiative"
        :initiative="initiative"
        :has-additional-assessment="hasAdditionalAssessment"
        :enable-edit-button="enableEditButton"
        :enable-show-button="enableShowButton"
        :enable-delete-button="enableDeleteButton"
        :enable-reassess-button="enableReassessButton"
        :enable-assess-button="enableAssessButton"
        :status-help-text="statusHelpText"
        :score-help-text="scoreHelpText"
        :expanded-start="expandedProjects[initiative.id]"
    />

</template>

<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'

import {computed, ref, watch, onMounted} from "vue";
import InitiativeListCard from "./InitiativeListCard.vue";
import {watchDebounced} from "@vueuse/core";
import VHelpTextLink from "./vHelpTextLink.vue";
import VHelpTextEntry from "./vHelpTextEntry.vue";


const props = defineProps({
    statusHelpText: Object,
    scoreHelpText: Object,
    organisation: Object,
    initialInitiatives: Object,
    hasAdditionalAssessment: Boolean,
    showAddButton: Boolean,
    showImportButton: Boolean,
    showExportButton: Boolean,
    enableEditButton: Boolean,
    enableShowButton: Boolean,
    enableDeleteButton: Boolean,
    enableReassessButton: Boolean,
    enableAssessButton: Boolean,
    settings: Object,
    expandedProjects: Object,
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
        id: 'code',
        label: 'Code',
    },
    {
        id: 'budget_org',
        label: 'Budget',
    },

])

// default value for sortBy and sortDir, which is sorted by name in ascending order
const sortBy = ref('name')
const sortDir = ref(1)

// enable sorting on any property
const propComparator = (propName, sortDir) =>
    (a, b) => a[propName] === b[propName] ? 0 : a[propName] < b[propName] ? -sortDir : sortDir

watch(sortDir, (newSortDir) => {
    filteredInitiatives.value.sort(propComparator(sortBy.value, newSortDir))
})

watch(sortBy, (newSortBy) => {
    filteredInitiatives.value.sort(propComparator(newSortBy, sortDir.value))
})


// status filter
const redlineStatusFilter = ref('');
const principleStatusFilter = ref('');
const portfolioFilter = ref('');

// keyword search filter
const searchString = ref('')


const portfolios = computed(() => {
    return initiatives.value.map(initiative => initiative.portfolio.name)
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

const initiatives = ref([])

const filteredInitiatives = computed(() => {

    // just mentioning variables, computed() function will be triggered when their value changed
    sortBy.value;
    sortDir.value;

    // declare temporary variables for filtering
    let tempInitiatives;
    tempInitiatives = initiatives.value;

    // apply filter for red flag status
    if (redlineStatusFilter.value) {
        tempInitiatives = tempInitiatives.filter(
            initiative => initiative.latest_assessment.redline_status === redlineStatusFilter.value
        )
    }

    // apply filter for principle status
    if (principleStatusFilter.value) {
        tempInitiatives = tempInitiatives.filter(
            initiative => initiative.latest_assessment.principle_status === principleStatusFilter.value
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
            || initiative.code.toLowerCase().includes(searchString.value.toLowerCase())
        )
    }

    return tempInitiatives;
})

// reset all session variables, then redirect to initiative page
async function resetFilters() {

    try {

        redlineStatusFilter.value = '';
        principleStatusFilter.value = '';
        portfolioFilter.value = '';
        sortBy.value = 'Name';
        sortDir.value = 1;
        searchString.value = '';

        const result = await axios.post("/admin/session/reset")

    } catch (error) {
        new Noty({
            type: "danger",
            text: "Something went wrong - the initiative filters could not be reset. Please reload the page and try again.",
        })
    }
}

// initialise settings by settings stored in session
function initialiseSettings(settings) {
    sortBy.value = settings.sortBy;
    sortDir.value = settings.sortDir;
    redlineStatusFilter.value = settings.redlineStatusFilterValue;
    principleStatusFilter.value = settings.principleStatusFilterValue;
    portfolioFilter.value = settings.portfolioFilter;
    searchString.value = settings.searchString;
}


// ##########################
// Handle Session Storage
// ##########################

watchDebounced(filteredInitiatives, () => {
    storeLatestSettings()
}, {
    debounce: 500,
    maxWait: 5000
})


function storeLatestSettings() {
    console.log('storing...')

    // send ajax call to SessionController.store
    const result = axios.post('/admin/session/store', {
        sortBy: sortBy.value,
        sortDir: sortDir.value,
        redlineStatusFilterValue: redlineStatusFilter.value == null ? '' : redlineStatusFilter.value,
        principleStatusFilterValue: principleStatusFilter.value == null ? '' : principleStatusFilter.value,
        portfolioFilter: portfolioFilter.value,
        searchString: searchString.value,
    });
}

function removeInitiative(initiative) {
    let tempArray = initiatives.value.map(initiative => initiative.id);

    let index = tempArray.indexOf(initiative.id);

    initiatives.value.splice(index, 1);
}

// replace reassessed initiative with new data;
async function refreshInitiative(newInitiative) {
    let tempArray = initiatives.value.map(initiative => initiative.id)

    let index = tempArray.indexOf(newInitiative.id)

    initiatives.value.splice(index, 1, newInitiative)

}

onMounted(() => {
    initiatives.value = [...props.initialInitiatives]
    initialiseSettings(props.settings);
})

</script>
