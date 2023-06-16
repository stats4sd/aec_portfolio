<template>


    <div
        class="card bg-light"
        :class="showFilters ? 'border-info' : ''"
    >
        <div class="card-body font-lg d-flex justify-content-between align-items-center px-12">
            <div style="min-width: 400px" class="d-flex align-items-center w-100">
                <b class="pr-3">SHOWING PORTFOLIO:</b>
                <vSelect
                    class="flex-grow-1"
                    v-model="filters.portfolio"
                    :options="portfolios"
                    :reduce="portfolio => portfolio.id"
                    :label="'name'"
                    placeholder="ALL PORTFOLIOS"
                    :clearable="true"
                ></vSelect>
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
                                    <b>MINIMUM BUDGET:</b>
                                    <input
                                        class="bg-white d-block py-1"
                                        v-model="filters.minBudget"

                                    />
                                    <p class="help-block text-error" style="height: 1em;" v-if="minBudgetError || maxBudgetError">{{ minBudgetError }}</p>
                                </div>
                                <div class="w-50">
                                    <b>MAXIMUM BUDGET:</b>
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
                    <span class="badge-pill badge-info" v-if="filters.minBudget && !filters.maxBudget"> USD {{ filters.minBudget }} or higher</span>
                    <span class="badge-pill badge-info" v-if="!filters.minBudget && filters.maxBudget"> USD {{ filters.maxBudget }} or lower</span>
                    <span class="badge-pill badge-info" v-if="filters.minBudget && filters.maxBudget"> USD {{ filters.minBudget }} - {{ filters.maxBudget }}</span>
                </div>
            </div>
            <div class="btn btn-primary" @click="showFilters = !showFilters">{{ showFilters ? 'Apply' : 'Modify' }} Filters</div>
        </div>
    </div>


    <div class="mt-8 card">
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
                            <span class="font-weight-bold ">{{ formatBudget(summary.totalBudget) }} USD</span>
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
                            <span class="font-weight-bold ">{{ formatBudget(summary.aeBudget) }}USD</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 card">
        <div class="card-header d-flex align-items-baseline">
            <h2 class="mr-4">Summary of Red Flags</h2>
            <h5>({{ filters.portfolio ? filters.portfolio.name.toUpperCase() : 'ALL PORTFOLIOS' }})</h5>
        </div>
        <div class="card-body">


            <!-- red lines summary -->
            <table class="table" v-if="summary.redlinesSummary != null">

                <thead class="text-center">
                <th>Red Lines</th>
                <th>Yours</th>
                <th>Others</th>
                </thead>

                <tbody class="text-center">
                <tr v-for='redlinesSummaryRecord in summary.redlinesSummary'>
                    <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.name }}</td>
                    <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.yours }}%</td>
                    <td :class="[ redlinesSummaryRecord.yours === 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.others }}%</td>
                </tr>
                </tbody>

            </table>

        </div>
    </div>

    <!-- principles summary -->
    <table class="table" v-if="summary.yoursPrinciplesSummarySorted != null">
        <thead>
        <th width="38%"></th>
        <th width="22%" align="center"><u>Yours</u></th>
        <th width="28%">
            <center><u>Others</u></center>
        </th>
        <th width="12%"></th>
        </thead>

        <tr>
            <td colspan="2">
                <div class="demo-container-1">
                    <div id="chart1" class="demo-placeholder"></div>
                </div>
            </td>
            <td>
                <div class="demo-container-2">
                    <div id="chart2" class="demo-placeholder"></div>
                </div>
            </td>
            <td valign="top">
                <br/>
                <div id="chart2Legend" class="legend"></div>
            </td>
        </tr>
    </table>

    <hr/>

    <div class="btn btn-danger" @click="checkSummary">
        Console Log Summary Output
    </div>

    <div class="btn btn-primary" @click="getData">
        Get Data Test
    </div>
</template>


<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import {ref, computed, onMounted, watch} from "vue";
import {isNumber} from "lodash";


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


// TEST
onMounted(() => {
    showFilters.value = true;
})


// handle summary data
const summary = ref({
    status: null,
    message: null,
    statusSummary: null,
    redlinesSummary: null,
    principleSummary: null,
    principleSummaryOthers: null,
})

async function getData() {

    filters.value.organisation_id = props.organisation.id

    const res = await axios.post("/admin/generic-dashboard/enquire", filters.value)
    summary.value = res.data


    // show principles summary by calling Javascript function
    window.setTimeout(showPrinciplesSummary, 1000);
}


function checkSummary() {
    console.clear()
    console.log(summary.value)
}


// format budgets

function formatBudget(amount) {

    return amount ? amount.toLocaleString() : '';

}


function showPrinciplesSummary() {
    // alert("showPrinciplesSummary()");

    // Features:
    // 1. prepare chart data
    // 2. define chart options for stacked bar chart
    // 3. show legend, to indicate which color for which principle
    // 4. y-axis, show AE principle name
    // 5. Hide percentage of each bar (center principle name but number goes to top location...)
    // 6. show legend in separate container

    // ======================================== //

    let record = [];
    let index;


    // yours principles summary sorted
    let chart1Data0 = [];
    let chart1Data1 = [];
    let chart1Data2 = [];

    for (let i = summary.value.yoursPrinciplesSummarySorted.length - 1; i >= 0; i--) {
        index = 13 - i;

        record = [summary.value.yoursPrinciplesSummarySorted[i].green, index];
        chart1Data0.push(record);

        record = [summary.value.yoursPrinciplesSummarySorted[i].yellow, index];
        chart1Data1.push(record);

        record = [summary.value.yoursPrinciplesSummarySorted[i].red, index];
        chart1Data2.push(record);
    }


    // others principles summary sorted
    let chart2Data0 = [];
    let chart2Data1 = [];
    let chart2Data2 = [];

    for (let i = summary.value.othersPrinciplesSummarySorted.length - 1; i >= 0; i--) {
        index = 13 - i;

        record = [summary.value.othersPrinciplesSummarySorted[i].green, index];
        chart2Data0.push(record);

        record = [summary.value.othersPrinciplesSummarySorted[i].yellow, index];
        chart2Data1.push(record);

        record = [summary.value.othersPrinciplesSummarySorted[i].red, index];
        chart2Data2.push(record);
    }


    // prepare chart 1 and chart 2 data
    let chart1Data = [];
    chart1Data[0] = {label: '1.5 - 2', color: '#54C45E', data: chart1Data0};
    chart1Data[1] = {label: '0.5 - 1.5', color: '#FFE342', data: chart1Data1};
    chart1Data[2] = {label: '0 - 0.5', color: '#FF0000', data: chart1Data2};

    let chart2Data = [];
    chart2Data[0] = {label: '1.5 - 2', color: '#54C45E', data: chart2Data0};
    chart2Data[1] = {label: '0.5 - 1.5', color: '#FFE342', data: chart2Data1};
    chart2Data[2] = {label: '0 - 0.5', color: '#FF0000', data: chart2Data2};

    // prepare chart 1 and chart 2 y axis ticks
    let chart1yAxisTicks = [];

    for (let i = 0; i < summary.value.yoursPrinciplesSummarySorted.length; i++) {
        let index = 13 - i;
        chart1yAxisTicks.push([index, summary.value.yoursPrinciplesSummarySorted[i].name]);
    }

    let chart2yAxisTicks = [
        [13, ''],
        [12, ''],
        [11, ''],
        [10, ''],
        [9, ''],
        [8, ''],
        [7, ''],
        [6, ''],
        [5, ''],
        [4, ''],
        [3, ''],
        [2, ''],
        [1, ''],
    ];

    // prepare chart 1 and chart 2 flag to show legend
    let chart1LegendFlag = false;
    let chart2LegendFlag = true;

    // prepare chart 1 and chart 2 Y axis label width
    let chart1YAxisLabelWidth = 250;
    let chart2YAxisLabelWidth = 0;

    console.log("#chart1", chart1Data)
    console.log('axisticks', chart1yAxisTicks)
    console.log('axis-label-width', chart1YAxisLabelWidth)
    console.log('legend-flag', chart1LegendFlag);

    // plot chart 1 with chart 1 data and options
    plotWithOptions("#chart1", chart1Data, chart1yAxisTicks, chart1YAxisLabelWidth, chart1LegendFlag, "");

    // plot chart 2 with chart 2 data and options
    plotWithOptions("#chart2", chart2Data, chart2yAxisTicks, chart2YAxisLabelWidth, chart2LegendFlag, "chart2Legend");
}

// chart options
function plotWithOptions(chartId, chartData, yAxisTicks, yAxisLabelWidth, legendFlag, legendId) {
    $.plot(chartId, chartData, {

        // stacked bar chart
        // need to include "jquery.flot.stack.js"
        series: {
            stack: true,
            lines: {
                show: false,
                fill: true,
                steps: false,

            },
            bars: {
                show: true,
                barWidth: 1,
                fill: 0.8,
                horizontal: true,
                align: 'center',

                // show number in bar segment
                // need to include "jquery.flot.barnumbers.js"
                numbers: {show: false}
            }
        },

        // customize x-axis labels
        // need to include "jquery.flot.axislabel.js"
        xaxis: {
            show: true,
            axisLabel: '% of Projects',
            tickLength: 15,
            tickSize: 50,
            tickFormatter: function (val, axis) {
                return val + '%';
            },

        },

        // customize y-axis labesl
        yaxis: {
            show: true,
            tickLength: 0,
            autoScale: "exact",
            ticks: yAxisTicks,
            labelWidth: yAxisLabelWidth,
        },

        grid: {
            borderWidth: 1,
        },

        // show legend
        // need to include "jquery.flot.legend.js"
        legend: {
            show: legendFlag,
            container: document.getElementById(legendId),
        },

    });
}


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
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 15px;
    padding-bottom: 10px;
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
