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
                <div class="btn btn-warning justify-end">
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
                            <h4 class="mb-4">Filter By Region / Country</h4>
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
                            <h4 class="mt-8 mb-4">Filter by Initiative Budget</h4>
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <div class="pr-8 pr-xl-16">
                                    <b>MINIMUM BUDGET ({{ organisation.currency }}):</b>
                                    <div class="d-flex align-items-center">

                                        <input
                                            class="bg-white d-block py-1 ml-2"
                                            v-model="filters.minBudget"
                                        />
                                    </div>
                                    <p class="help-block text-error" style="height: 1em;" v-if="minBudgetError || maxBudgetError">{{ minBudgetError }}</p>
                                </div>
                                <div class="w-50">
                                    <b>MAXIMUM BUDGET ({{ organisation.currency }}):</b>
                                    <input
                                        class="bg-white d-block py-1"
                                        v-model="filters.maxBudget"
                                    />
                                    <p class="help-block text-error" style="height: 1em;" v-if="minBudgetError || maxBudgetError">{{ maxBudgetError }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-expand-transition>
        <div class="card-footer bg-blue-lighten-4 d-flex justify-content-between align-items-center px-12 font-lg">
            <b class="pr-8">Filters:</b>
            <div class="d-flex flex-wrap justify-content-start">
                <span class="text-dark pr-8" v-if="anyFilters === 0">
                    NO FILTERS APPLIED
                </span>

                <div v-if="filters.regions" class="px-2 mb-1 d-flex flex-wrap">
                    REGIONS:
                </div>
                <span
                    v-for="region in filters.regions"
                    class="badge-pill badge-info mr-2 mb-1">
                        {{ region.name }}
                    </span>

                <div v-if="filters.countries" class="px-2 mb-1 d-flex flex-wrap">
                    <span class="pr-2">COUNTRIES:</span>
                </div>
                <div
                    v-for="country in filters.countries"
                    class="badge-pill badge-info mr-2 mb-1">
                    {{ country.name }}
                </div>
                <div v-if="filters.startDate || filters.endDate" class="mr-3 mb-1">
                    <span class="px-2">DATES:</span>
                    <span class="badge-pill badge-info" v-if="filters.startDate && !filters.endDate"> {{ filters.startDate }} onwards</span>
                    <span class="badge-pill badge-info" v-if="! filters.startDate && filters.endDate"> {{ filters.startDate }} and earlier</span>
                    <span class="badge-pill badge-info" v-if="filters.startDate && filters.endDate">{{ filters.startDate }} - {{ filters.endDate }}</span>
                </div>

                <div v-if="(filters.minBudget || filters.maxBudget) && !minBudgetError && !maxBudgetError" class="mr-3 mb-1">
                    <span class="px-2">BUDGET:</span>
                    <span class="badge-pill badge-info" v-if="filters.minBudget && !filters.maxBudget"> {{ organisation.currency }} {{ filters.minBudget }} or higher</span>
                    <span class="badge-pill badge-info" v-if="!filters.minBudget && filters.maxBudget"> {{ organisation.currency }} {{ filters.maxBudget }} or lower</span>
                    <span class="badge-pill badge-info" v-if="filters.minBudget && filters.maxBudget"> {{ organisation.currency }} {{ filters.minBudget }} - {{ filters.maxBudget }}</span>
                </div>
            </div>
            <div class="btn btn-warning text-dark" @click="resetFilters" v-if="anyFilters">Reset Filters</div>
            <div class="btn btn-primary" @click="modifyFilters">{{ showFilters ? 'Apply' : 'Modify' }} Filters</div>
        </div>
    </div>

    <div v-if="summary.tooFewOtherProjects" class="alert alert-warning text-dark">
        NOTE: The chosen filters have resulted in a comparison dataset too small to guarantee anonymity, so the "other instutitions" results will not be shown.
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
                                <li class="list-group-item d-flex font-lg">
                                    <span class="w-50 text-right pr-4"># Initiatives Added</span>
                                    <span class="font-weight-bold ">{{ summary.totalCount }}</span>
                                </li>
                                <li
                                    v-for="summaryLine in summary.statusSummary"
                                    class="list-group-item d-flex font-lg text-deep-green">
                                    <span class="w-50 text-right pr-4">{{ summaryLine.status }}</span>
                                    <span class="font-weight-bold ">{{ summaryLine.number }} ({{ summaryLine.percent }}%)</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-6 p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg">
                                    <span class="w-50 text-right pr-4">Total Budget</span>
                                    <span class="font-weight-bold ">{{ formatBudget(summary.totalBudget) }} {{ organisation.currency }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-footer p-0 bg-light-success">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg">
                                    <span class="w-50 text-right pr-4">OVERALL SCORE</span>
                                    <span class="font-weight-bold">{{ summary.assessmentScore }}%</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex font-lg">
                                    <span class="w-50 text-right pr-4">AE-focused Budget</span>
                                    <span class="font-weight-bold ">{{ formatBudget(summary.aeBudget) }} {{ organisation.currency }}</span>
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
                </div>
                <div class="card-body">


                    <!-- red lines summary -->
                    <table class="table" v-if="summary.redlinesSummary != null">

                        <thead class="text-center">
                        <th>Red Flags</th>
                        <th>Yours</th>
                        <th>Others</th>
                        </thead>

                        <tbody class="text-center">
                        <tr v-for='redlinesSummaryRecord in summary.redlinesSummary'>
                            <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.name }}</td>
                            <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.yours }}%</td>
                            <td v-if="!summary.tooFewOtherProjects"
                                :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.others }}%
                            </td>
                        </tr>
                        </tbody>

                    </table>

                    <table class="table" v-if="summary.statusSummary != null">
                        <tbody class="text-center">
                        <tr v-for='summaryLine in summary.statusSummary'>
                            <td v-if="summaryLine.status == 'Passed all red flags'" class="list-group-item d-flex font-lg text-deep-green">
                                <span class="w-50 text-right pr-4">{{ summaryLine.status }}</span>
                                <span class="font-weight-bold ">{{ summaryLine.number }} ({{ summaryLine.percent }}%)</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div v-if="tab==='principles'">
            <div class="mx-auto mt-8 w-100" style="max-width: 1500px;" v-if="summary.yoursPrinciplesSummarySorted">
                <div class="card-header">
                    <h2 class="mr-4">Summary of Principles</h2>
                    <h5>({{ filters.portfolio ? filters.portfolio.name.toUpperCase() : 'ALL PORTFOLIOS' }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6 d-flex flex-column align-items-center">
                            <h2 class="mb-4">Your Initiatives</h2>
                            <Bar :data="chartDataYours" :options="chartOptions"/>
                        </div>

                        <div class="col-12 col-lg-6 d-flex flex-column align-items-center">
                            <h2 class="mb-4">Other Institutions' Initiatives</h2>
                            <Bar v-if="!summary.tooFewOtherProjects" :data="chartDataOthers" :options="chartOptions"/>

                            <div v-else class="d-flex flex-column justify-content-center h-100">
                                <div class="alert alert-info text-dark">There are too few initiatives or institutions within the current set of filters to display anonymized results.</div>
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
    regions: {
        type: Array,
        default: () => [],
    },
    countries: {
        type: Array,
        default: () => [],
    }

})

const portfolios = computed(() => {
    return props.organisation.portfolios ?? []
})


// filters
const showFilters = ref(false)

const filters = ref({
    portfolio: null,
    regions: null,
    countries: null,
    startDate: null,
    endDate: null,
    minBudget: null,
    maxBudget: null,
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
    Object.keys(filters.value).forEach(key => filters.value[key] = null);
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

    const res = await axios.post("/admin/generic-dashboard/enquire", data)
    summary.value = res.data


}

function formatBudget(amount) {
    return amount ? amount.toLocaleString() : '';
}


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
// import ChartDataLabels from 'chartjs-plugin-datalabels';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

// ChartJS.register(ChartDataLabels);

function getChartData(principlesSummary) {
    return {
        labels: principlesSummary.map(item => item.name),
        datasets: [
            {
                label: 'EXCELLENT (1.5 - 2.0)',
                backgroundColor: '#54C45E',
                data: principlesSummary.map(item => item.green)
            },
            {
                label: 'OK (0.5 - 1.5)',
                backgroundColor: '#FFE342',
                data: principlesSummary.map(item => item.yellow)
            },
            {
                label: 'POOR (0.0 - 0.5)',
                backgroundColor: '#e3342f',
                data: principlesSummary.map(item => item.red)
            },
        ]
    }
}

const chartDataYours = computed(() => {

    if (summary.value.yoursPrinciplesSummarySorted) {
        return getChartData(summary.value.yoursPrinciplesSummarySorted)
    }
    return {
        labels: [],
        datasets: [],
    }
})

const chartDataOthers = computed(() => {

    if (summary.value.othersPrinciplesSummarySorted && !summary.value.tooFewOtherProjects) {
        return getChartData(summary.value.othersPrinciplesSummarySorted)
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
                mirror: true,
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
    },
    plugins: {
        // datalabels: {
        //     color: 'black',
        //     display: function (context) {
        //         return context.dataset.data[context.dataIndex] > 10; // hide label if bar is too short
        //     },
        //     font: {
        //         weight: 'bold'
        //     },
        //     formatter: function (value, context) {
        //         return Math.round(value) + "%"
        //     }
        // },

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
