<template>


    <div class="container">
    </div>
    <!-- FILTERS -->
    <div
        class="card bg-light"
        :class="showFilters ? 'border-info' : ''"
    >
        <div class="card-body font-lg d-flex justify-content-between align-items-center px-12">
            <div style="min-width: 400px" class="d-flex align-items-center w-100">
                <b class="pr-3">SHOWING PORTFOLIO:</b>
                <vSelect
                    class="flex-grow-1 mr-16"
                    v-model="filters.portfolio"
                    :options="portfolios"
                    :label="'name'"
                    placeholder="ALL PORTFOLIOS"
                    :clearable="true"
                ></vSelect>
                <div class="btn btn-warning justify-end" @click="filters.portfolio = null">
                    Show all portfolios
                </div>

            </div>
            <div>

            </div>
        </div>
        <v-expand-transition>
            <div v-if="showFilters">
                <div class="card-body bg-blue-lighten-4">
                    <div class="row">
                        <div class="col-lg-6 col-12 px-12">
                            <h4 class="mb-4">Filter By Geographic Reach / Region / Country</h4>
                            <b>GEOGRAPHIC REACH:</b>
                            <v-select
                                class="bg-white mb-8"
                                v-model="filters.greaches"
                                :options="['Global level', 'Multi-country level', 'Country level']"
                                label="name"
                                placeholder="SELECT GEOGRAPHIC REACH"
                                multiple="true"
                                :clearable="true"
                            />

                            <b>REGIONS:</b>
                            <v-select
                                class="bg-white mb-8"
                                v-model="filters.regions"
                                :options="regions"
                                label="name"
                                placeholder="SELECT REGIONS"
                                multiple="true"
                                :clearable="true"
                            />

                            <b>COUNTRIES:</b>
                            <v-select
                                v-model="filters.countries"
                                class="bg-white"
                                :options="filteredCountries"
                                label="name"
                                placeholder="SELECT COUNTRIES"
                                multiple="true"
                                :clearable="true"
                            />
                            <hr/>
                            <h4>Filter by Initiative Category</h4>
                            <b>Categories</b>
                            <v-select
                                v-model="filters.categories"
                                class="bg-white"
                                :options="categories"
                                label="name"
                                placeholder="SELECT CATEGORIES"
                                multiple="true"
                                :clearable="true"
                            />
                        </div>
                        <div class="col-lg-6 col-12 px-12 mt-lg-0 mt-8">
                            <h4 class="mb-4">Filter by Start Date</h4>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pr-8 pr-xl-16">
                                    <b>FROM:</b>
                                    <vue-date-picker
                                        v-model="filters.startDate"
                                        year-picker
                                        auto-apply
                                        text-input
                                        :max-date="filters.endDate ? new Date(filters.endDate + '-01-01') : null"
                                    />
                                </div>
                                <div>
                                    <b>TO:</b>
                                    <vue-date-picker
                                        v-model="filters.endDate"
                                        year-picker
                                        auto-apply
                                        text-input
                                        :min-date="filters.startDate ? new Date(filters.startDate + '-01-01') : null"
                                    />
                                </div>
                            </div>
                            <hr/>
                            <h4 class="mt-8 mb-0">Filter by Initiative Budget</h4>
                            <p class="text-sm mb-4">For comparison with other organisations, the filters you enter here will be converted to EUR and then used to filter the aggregated results from other organisations.</p>
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <div class="pr-8 pr-xl-16">
                                    <b>MINIMUM BUDGET ({{ organisation.currency }}):</b>
                                    <div class="d-flex align-items-center">

                                        <input
                                            class="bg-white d-block py-1 ml-2"
                                            v-model="filters.minBudget"
                                        />
                                    </div>
                                    <p class="help-block text-error" style="height: 1em;"
                                       v-if="minBudgetError || maxBudgetError">{{ minBudgetError }}</p>
                                </div>
                                <div class="w-50">
                                    <b>MAXIMUM BUDGET ({{ organisation.currency }}):</b>
                                    <input
                                        class="bg-white d-block py-1"
                                        v-model="filters.maxBudget"
                                    />
                                    <p class="help-block text-error" style="height: 1em;"
                                       v-if="minBudgetError || maxBudgetError">{{ maxBudgetError }}</p>
                                </div>
                            </div>

                            <hr/>
                            <h4>Filter Other institutions by Institution Type</h4>
                            <b>Institution Types</b>
                            <v-select
                                v-model="filters.itypes"
                                class="bg-white"
                                :options="itypes"
                                label="name"
                                placeholder="SELECT INSTITUTION TYPES"
                                multiple="true"
                                :clearable="true"
                            />


                        </div>
                    </div>
                </div>
            </div>
        </v-expand-transition>
        <div class="card-footer bg-blue-lighten-4 d-flex justify-content-between align-items-center px-12 font-lg">
            <b class="pr-8">Filters:</b>
            <div class="d-flex flex-wrap justify-content-start">
                <span class="text-dark pr-8" v-if="anyFilters == 0">
                    NO FILTERS APPLIED
                </span>

                <div v-if="filters.greaches && filters.greaches.length > 0" class="px-2 mb-1 d-flex flex-wrap">
                    GEOGRAPHIC REACH:
                </div>
                <span
                    v-for="greach in filters.greaches"
                    class="badge-pill badge-info mr-2 mb-1">
                        {{ greach }}
                </span>

                <div v-if="filters.regions && filters.regions.length > 0" class="px-2 mb-1 d-flex flex-wrap">
                    REGIONS:
                </div>
                <span
                    v-for="region in filters.regions"
                    class="badge-pill badge-info mr-2 mb-1">
                        {{ region.name }}
                </span>

                <div v-if="filters.countries && filters.countries.length > 0" class="px-2 mb-1 d-flex flex-wrap">
                    <span class="pr-2">COUNTRIES:</span>
                </div>
                <div
                    v-for="country in filters.countries"
                    class="badge-pill badge-info mr-2 mb-1">
                    {{ country.name }}
                </div>

                <div v-if="filters.categories && filters.categories.length > 0" class="px-2 mb-1 d-flex flex-wrap">
                    INITIATIVE CATEGORIES:
                </div>
                <span
                    v-for="category in filters.categories"
                    class="badge-pill badge-info mr-2 mb-1">
                        {{ category.name }}
                </span>


                <div v-if="filters.startDate || filters.endDate" class="mr-3 mb-1">
                    <span class="px-2">DATES:</span>
                    <span class="badge-pill badge-info"
                          v-if="filters.startDate && !filters.endDate"> {{ filters.startDate }} onwards</span>
                    <span class="badge-pill badge-info"
                          v-if="! filters.startDate && filters.endDate"> {{ filters.startDate }} and earlier</span>
                    <span class="badge-pill badge-info" v-if="filters.startDate && filters.endDate">{{
                            filters.startDate
                        }} - {{ filters.endDate }}</span>
                </div>

                <div v-if="(filters.minBudget || filters.maxBudget) && !minBudgetError && !maxBudgetError"
                     class="mr-3 mb-1">
                    <span class="px-2">BUDGET:</span>
                    <span class="badge-pill badge-info" v-if="filters.minBudget && !filters.maxBudget"> {{
                            organisation.currency
                        }} {{ filters.minBudget }} or higher</span>
                    <span class="badge-pill badge-info" v-if="!filters.minBudget && filters.maxBudget"> {{
                            organisation.currency
                        }} {{ filters.maxBudget }} or lower</span>
                    <span class="badge-pill badge-info" v-if="filters.minBudget && filters.maxBudget"> {{
                            organisation.currency
                        }} {{ filters.minBudget }} - {{ filters.maxBudget }}</span>
                </div>

                <div v-if="filters.itypes && filters.itypes.length > 0" class="px-2 mb-1 d-flex flex-wrap">
                    INSTITUTION TYPES:
                </div>
                <span
                    v-for="itype in filters.itypes"
                    class="badge-pill badge-info mr-2 mb-1">
                        {{ itype.name }}
                </span>

            </div>
            <div class="btn btn-warning text-dark" @click="resetFilters" v-if="anyFilters != 0">Reset Filters</div>
            <div class="btn btn-primary" @click="modifyFilters">{{ showFilters ? 'Apply' : 'Modify' }} Filters</div>
        </div>
    </div>

    <div v-if="summary.status != 0" class="alert alert-warning text-dark">
        {{ summary.message }}
    </div>

    <div v-if="summary.tooFewOtherProjects" class="alert alert-warning text-dark">
        NOTE: The chosen filters have resulted in a comparison dataset too small to guarantee anonymity, so the "other
        instutitions" results will not be shown.
    </div>

    <ul class="nav nav-tabs mt-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button
                class="nav-link p-3"
                :class="tab==='summary' ? 'active' : ''"
                type="button"
                role="tab"
                @click="tab = 'summary'"
            >
                <h5 class="text-uppercase m-0 p-0">Summary of Initiatives</h5>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button
                class="nav-link p-3"
                :class="tab==='redflags' ? 'active' : ''"
                type="button"
                role="tab"
                @click="tab = 'redflags'"
            >
                <h5 class="text-uppercase m-0 p-0">Summary of Red Flags</h5>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button
                class="nav-link p-3"
                :class="tab==='principles' ? 'active' : ''"
                type="button"
                role="tab"
                @click="tab = 'principles'"
            >
                <h5 class="text-uppercase m-0 p-0">Summary of Principles</h5>
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <div class='tab pane' v-if="tab==='summary'">

            <div class="mt-8">
                <div class="card-header d-flex align-items-baseline">
                    <h2 class="mr-4">Summary of Initiatives</h2>
                    <h5>({{ filters.portfolio ? filters.portfolio.name.toUpperCase() : 'ALL PORTFOLIOS' }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6 p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg align-items-center">
                                    <span class="w-50 text-right pr-4">
                                        # Initiatives Added
                                    </span>
                                    <span class="font-weight-bold ">{{ summary.totalCount }}</span>
                                    <v-help-text-link class="pl-2 font-lg" location="Dashboard - initiatives added" type="popover"/>
                                </li>
                                <li
                                    v-for="summaryLine in summary.statusSummary"
                                    class="list-group-item d-flex font-lg align-items-center"
                                    :class="summaryLine.status === 'Failed at least one red flag' ? 'text-red' : 'text-deep-green'">
                                    <span class="w-50 text-right pr-4">{{ summaryLine.status }}</span>
                                    <span class="w-25 font-weight-bold ">{{ summaryLine.number }} ({{
                                            summaryLine.percent
                                        }}%)</span>
                                    <v-help-text-link class="pl-2 font-lg" :location="'Dashboard - '+summaryLine.status" type="popover"/>

                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-6 p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg">
                                    <span class="w-50 text-right pr-4">Total Budget</span>
                                    <span class="font-weight-bold ">{{
                                            formatBudget(summary.totalBudget)
                                        }} {{ organisation.currency }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-footer p-0 bg-light-success">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg align-items-center">
                                    <span class="w-50 text-right pr-4">OVERALL SCORE</span>
                                    <span class="font-weight-bold">{{ summary.assessmentScore }}%</span>
                                    <v-help-text-link class="pl-2 font-lg" location="Dashboard - overall score" type="popover"/>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg align-items-center">
                                    <span class="w-50 text-right pr-4">AE-focused Budget</span>
                                    <span class="font-weight-bold ">{{
                                            formatBudget(summary.aeBudget)
                                        }} {{ organisation.currency }}</span>
                                    <v-help-text-link class="pl-2 font-lg" location="Dashboard - AE focused budget" type="popover" data-placement="top"/>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="tab==='redflags'">
            <div class="mt-8">
                <div class="card-header d-flex align-items-baseline">
                    <h2 class="mr-4">Summary of Red Flags</h2>
                    <h5>({{ filters.portfolio ? filters.portfolio.name.toUpperCase() : 'ALL PORTFOLIOS' }})</h5>
                    <v-help-text-link class="pl-2 font-lg" location="Dashboard - Redflags summary"/>
                </div>
                <div class="card-body">
                    <v-help-text-entry location="Dashboard - Redflags summary"/>

                    <!-- red lines summary -->
                    <table class="table" v-if="summary.redlinesSummary != null">

                        <thead class="text-center">
                        <th>Red Flags</th>
                        <th>Yours</th>
                        <th>Others</th>
                        </thead>

                        <tbody class="text-center">
                        <tr v-for='redlinesSummaryRecord in summary.redlinesSummary'>
                            <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">
                                {{ redlinesSummaryRecord.name }}
                            </td>
                            <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">
                                {{ redlinesSummaryRecord.yours }}%
                            </td>
                            <td v-if="!summary.tooFewOtherProjects"
                                :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">
                                {{ redlinesSummaryRecord.others }}%
                            </td>
                        </tr>
                        </tbody>

                    </table>

                    <table class="table" v-if="summary.statusSummary != null">
                        <tbody class="text-center">
                        <tr v-for='summaryLine in summary.statusSummary'>
                            <td v-if="summaryLine.status == 'Passed all red flags'"
                                class="list-group-item d-flex font-lg text-deep-green">
                                <span class="w-50 text-right pr-4">{{ summaryLine.status }}</span>
                                <span class="font-weight-bold ">{{ summaryLine.number }} ({{
                                        summaryLine.percent
                                    }}%)</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div v-if="tab==='principles'">
            <div class="mx-auto mt-8 w-100" style="max-width: 1500px;" v-if="summary.yoursPrinciplesSummarySorted">
                <div class="card-header d-flex align-items-baseline">
                    <h2 class="mr-4">Summary of Principles</h2>
                    <h5>({{ filters.portfolio ? filters.portfolio.name.toUpperCase() : 'ALL PORTFOLIOS' }})</h5>
                    <v-help-text-link class="pl-2 font-lg" location="Dashboard - Summary of Principles"/>

                </div>
                <div class="card-body">
                    <v-help-text-entry location="Dashboard - Summary of Principles"/>

                    <div class="row">
                        <div class="col-12 col-lg-6 d-flex flex-column align-items-center">
                            <h2 class="mb-4">Your Initiatives</h2>
                            <Bar :data="chartDataYours" :options="chartOptions"/>
                            <div class="text-center mt-4 px-4">
                                <h5 class="mb-0">Percentage of Initiatives scoring at different levels by principle</h5>
                                <p>Calculated using the number of initiatives for which the principle applied as the denominator.</p>
                                <p>The grey lines under the name of the principle represent the % of initiatives that marked that principle as non-applicable </p>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 d-flex flex-column align-items-center">
                            <h2 class="mb-4">Other Institutions' Initiatives</h2>
                            <Bar v-if="!summary.tooFewOtherProjects" :data="chartDataOthers" :options="chartOptions"/>
                            <div v-if="!summary.tooFewOtherProjects" class="text-center mt-4 px-4">
                                <h5 class="mb-0">Percentage of Initiatives scoring at different levels by principle</h5>
                                <p>Calculated using the number of initiatives for which the principle applied as the denominator.</p>
                                <p>The grey lines under the name of the principle represent the % of initiatives that marked that principle as non-applicable </p>
                            </div>
                            <div v-else class="d-flex flex-column justify-content-center h-100">
                                <div class="alert alert-info text-dark">There are too few initiatives or institutions
                                    within the current set of filters to display anonymized results.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'

import VHelpTextLink from "./vHelpTextLink.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import {ref, computed, onMounted, watch} from "vue";
import {isNumber} from "lodash";

const tab = ref('summary')

const props = defineProps({
    user: {
        type: Object,
        default: () => {
        },
    },
    organisation: {
        type: Object,
        default: () => {
        },
    },
    greaches: {
        type: Array,
        default: () => [],
    },
    regions: {
        type: Array,
        default: () => [],
    },
    countries: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    itypes: {
        type: Array,
        default: () => [],
    },
})

const portfolios = computed(() => {
    return props.organisation.portfolios ?? []
})


// filters
const showFilters = ref(false)

const filters = ref({
    portfolio: null,
    greaches: null,
    regions: null,
    countries: null,
    categories: null,
    startDate: null,
    endDate: null,
    minBudget: null,
    maxBudget: null,
    itypes: null,
})

const minBudgetError = computed(() => {
    if (isNaN(Number(filters.value.minBudget))) return 'Please enter a valid number';

    if (filters.value.minBudget && filters.value.maxBudget && Number(filters.value.minBudget) > Number(filters.value.maxBudget)) return 'Please ensure the budget range is valid';

    return ''
})
const maxBudgetError = computed(() => {
    if (isNaN(Number(filters.value.maxBudget))) return 'Please enter a valid number';

    if (filters.value.minBudget && filters.value.maxBudget && Number(filters.value.minBudget) > Number(filters.value.maxBudget)) return 'Please ensure the budget range is valid';

    return ''
})

const filteredCountries = computed(() => {
    return props.countries.filter(country => filters.value.regions ? filters.value.regions.map(region => region.id).includes(country.region_id) : false)
})

function modifyFilters() {
    showFilters.value = !showFilters.value

    if (!showFilters.value) {
        getData()
    }
}

const anyFilters = computed(() => {
    return Object.keys(filters.value).some(key => filters.value[key] !== null)
})

function resetFilters() {
    Object.keys(filters.value).forEach(key => {
        if (key !== "portfolio") filters.value[key] = null
    });
    getData()
}

onMounted(() => {
    getData()
})


// handle summary data
const summary = ref({
    status: null,
    message: null,
    statusSummary: null,
    redlinesSummary: null,
    yoursPrinciplesSummarySorted: null,
    othersPrinciplesSummarySorted: null,
})

async function getData() {
    let data = {...filters.value}

    data.organisation_id = props.organisation.id

    const res = await axios.post("/admin/dashboard/enquire", data)
    summary.value = res.data
}

function formatBudget(amount) {
    return amount ? amount.toLocaleString() : '';
}

watch(filters.value, () => getData())


// **************** ChartJS ****************
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'
import {Bar} from 'vue-chartjs'
import VHelpTextEntry from "./vHelpTextEntry.vue";

// import ChartDataLabels from 'chartjs-plugin-datalabels';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

// ChartJS.register(ChartDataLabels);

function getChartData(principlesSummary, nas) {

    return {
        labels: principlesSummary.map(item => item.name),
        datasets: [
            // add fake, transparent datasets onto the yNa axis above the real NA line. This pushes the NA line down so that it sits underneath the principle name.
            // without these, the NA line will be centered in the axis and overlap with the label.
            {
                backgroundColor: 'transparent',
                data: nas.map(item => 0),
                stack: 'another-one',
                grouped: true,
                yAxisID: 'yNa',
            },
            {
                backgroundColor: 'transparent',
                data: nas.map(item => 0),
                stack: 'another-one',
                grouped: true,
                yAxisID: 'yNa',
            },
            {
                backgroundColor: 'transparent',
                data: nas.map(item => 0),
                stack: 'another-one',
                grouped: true,
                yAxisID: 'yNa',
            },
            {
                backgroundColor: 'transparent',
                data: nas.map(item => 0),
                stack: 'another-one',
                grouped: true,
                yAxisID: 'yNa',
            },
            {
                label: 'NA',
                backgroundColor: '#bbb',
                data: nas.map(item => item.value),
                barThickness: 4,
                stack: 'na',
                grouped: true,
                yAxisID: 'yNa',
                legend: false,
            },
            {
                label: 'EXCELLENT (1.5 - 2.0)',
                backgroundColor: '#54C45E',
                data: principlesSummary.map(item => item.green),
                barPercentage: 1,
                categoryPercentage: 1,
                stack: 'main',
                grouped: true,
            },
            {
                label: 'OK (0.5 - 1.5)',
                backgroundColor: '#FFE342',
                data: principlesSummary.map(item => item.yellow),
                barPercentage: 1,
                categoryPercentage: 1,
                stack: 'main',
                grouped: true,
            },
            {
                label: 'POOR (0.0 - 0.5)',
                backgroundColor: '#e3342f',
                data: principlesSummary.map(item => item.red),
                barPercentage: 1,
                categoryPercentage: 1,
                stack: 'main',
                grouped: true,
            },
        ]
    }
}

const chartDataYours = computed(() => {

    if (summary.value.yoursPrinciplesSummarySorted) {
        return getChartData(summary.value.yoursPrinciplesSummarySorted, summary.value.yourNas)
    }
    return {
        labels: [],
        datasets: [],
    }
})

const chartDataOthers = computed(() => {

    if (summary.value.othersPrinciplesSummarySorted && !summary.value.tooFewOtherProjects) {
        return getChartData(summary.value.othersPrinciplesSummarySorted, summary.value.otherNas)
    }
    return {
        labels: [],
        datasets: [],
    }
})

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 1,
    indexAxis: 'y',
    scales: {
        y: {
            stacked: true,
            ticks: {
                min: 50,
                mirror: true,
                align: 'end',
                z: 1,
                color: 'black',
                font: {
                    size: 14,
                },
            },
        },
        x: {
            stacked: true,
            max: 100,
        },
        yNa: {
            display: false,
            stacked: false,
            max: 100,
        },
    },
    plugins: {
        tooltip: {
            padding: 14,
            backgroundColor: '#f8f9fa',
            titleColor: 'black',
            titleFont: {
                size: 14,
            },
            bodyColor: 'black',
            bodyFont: {
                size: 14,
            },
            xAlign: 'center',
            yAlign: 'center',
            callbacks: {
                label: (context) => context.formattedValue,
            },
        },
        legend: {
            labels: {
                // filter out the first dataset label (that dataset is purely to move the 'na' line down slightly)
                filter: function (item, chart) {
                    return item.datasetIndex > 3
                }
            }
        }
    },
    barPercentage: 1,
    categoryPercentage: 1,
    barThickness: 'flex',
    borderWidth: 1,
    borderColor: 'lightgrey',
})

</script>

<style>
.slide-fade-enter-active {
    transition: all 0.2s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}


.demo-container-1 {
    box-sizing: border-box;
    width: 594px;
    height: 450px;
    padding: 20px 15px 15px 15px;
    margin: 15px auto 30px auto;
}

.demo-container-2 {
    box-sizing: border-box;
    width: 350px;
    height: 450px;
    padding: 20px 15px 15px 15px;
    margin: 15px auto 30px auto;
}

.demo-placeholder {
    width: 100%;
    height: 100%;
    font-size: 14px;
}

.legend {
    display: block;
    -webkit-padding-start: 2px;
    -webkit-padding-end: 2px;
    border-width: initial;
    border-style: none;
    border-color: initial;
    border-image: initial;
    padding: 15px 10px 10px;
    font-size: 14px;
}

.legendLayer .background {
    fill: rgba(255, 255, 255, 0.65);
    stroke: rgba(0, 0, 0, 0.85);
    stroke-width: 1;
}

.tickLabel {
    font-size: 90%
}

</style>
