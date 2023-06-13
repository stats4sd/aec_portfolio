<template>


    <div class="mt-8 card">
        <div class="card-header">
            <h2>Summary of Initiatives</h2>
            <h5 class="text-uppercase">All portfolios</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-6 p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex font-xl">
                            <span class="w-50 text-right pr-4"># Created</span>
                            <span class="font-weight-bold ">500</span>
                        </li>
                        <li class="list-group-item d-flex font-xl text-deep-green">
                            <span class="w-50 text-right pr-4"># Passed all Redlines</span>
                            <span class="font-weight-bold ">475 (95%)</span>
                        </li>
                        <li class="list-group-item d-flex font-xl text-deep-green">
                            <span class="w-50 text-right pr-4"># Fully Assessed</span>
                            <span class="font-weight-bold ">400 (80%)</span>
                        </li>
                    </ul>
                </div>
                <div class="col-6 p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex font-xl">
                            <span class="w-50 text-right pr-4">Total Budget</span>
                            <span class="font-weight-bold ">7,269,000 USD</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-footer p-0 bg-light-success">
            <div class="row">
                <div class="col-6 pr-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex font-xl p-3">
                            <span class="w-25 text-right pr-4">OVERALL SCORE</span>
                            <span class="font-weight-bold ">86%</span>
                        </li>
                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex font-xl">
                            <span class="w-50 text-right pr-4">AE-focused Budget</span>
                            <span class="font-weight-bold ">6,251,340 USD</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</template>


<script>

export default {

    props: {
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
        portfolios: {
            type: Object,
            default: () => {
            },
        },
        regions: {
            type: Object,
            default: () => {
            },
        },
        countries: {
            type: Object,
            default: () => {
            },
        },
    },

    components: {},

    data() {
        return {
            // form variables
            formData: {},

            // countries for selected region
            filteredCountries: [],

            // enquire result
            enquireResult: null,
            status: '',
            message: '',
            statusSummary: null,
            redlinesSummary: null,
            yoursPrinciplesSummarySorted: null,
            othersPrinciplesSummarySorted: null,
        }
    },

    // triggered when Vue component is loaded
    mounted() {
        // set default values
        this.formData['portfolio'] = 0;
        this.formData['organisation'] = this.organisation.id;
        this.formData['projectStartFrom'] = '2020';
        this.formData['projectStartTo'] = '2030';
        this.formData['budgetFrom'] = '100000';
        this.formData['budgetTo'] = '10000000';
        this.formData['sortBy'] = 1;
    },

    computed: {},

    watch: {
        // monitor the value of variable

    },

    // custom methods for Vue component
    methods: {

        changeRegion() {
            // clear selected country
            this.formData['country'] = '';

            // clear filtered countries
            this.filteredCountries = this.regions.filter((region) => region.id === this.formData['region'])[0]['countries'];

        },

        validateCriteria() {
            // alert("validateCriteria()");

            var message = "";
            message += "Organisation: " + this.formData['organisation'] + "\n";
            message += "Portfolio: " + this.formData['portfolio'] + "\n";
            message += "Region: " + this.formData['region'] + "\n";
            message += "Country: " + this.formData['country'] + "\n";
            message += "projectStartFrom: " + this.formData['projectStartFrom'] + "\n";
            message += "projectStartTo: " + this.formData['projectStartTo'] + "\n";
            message += "budgetFrom: " + this.formData['budgetFrom'] + "\n";
            message += "budgetTo: " + this.formData['budgetTo'] + "\n";
            message += "========== " + "\n";
            message += "chkRegion: " + this.formData['chkRegion'] + "\n";
            message += "chkCountry: " + this.formData['chkCountry'] + "\n";
            message += "chkProjectStart: " + this.formData['chkProjectStart'] + "\n";
            message += "chkBudget: " + this.formData['chkBudget'] + "\n";

            // alert(message);

            // user must select a portfolio
            if (this.formData['portfolio'] == null) {
                alert("Please select a portfolio");
                return;
            }

            // if region checkbox is ticked, user must select a region
            if (this.formData['chkRegion'] == true && this.formData['region'] == null) {
                alert("Please select a region");
                return;
            }

            // if country checkbox is ticked, user must select a country
            if (this.formData['chkCountry'] == true && this.formData['country'] == null) {
                alert("Please select a country");
                return;
            }

            // if project start checkbox is ticked, project start from and project start to must have value
            if (this.formData['chkProjectStart'] == true && this.formData['projectStartFrom'] == "" || this.formData['projectStartTo'] == "") {
                alert("Please fill in both Project Start From and Project Start To");
                return;
            }

            // project start from must smaller than project start to
            if (this.formData['chkProjectStart'] == true && this.formData['projectStartFrom'] > this.formData['projectStartTo']) {
                alert("Project Start From should be smaller or equal to Project Start To");
                return;
            }

            // TBC: reasonable check for project start from and project start to (user may manually enter something not reasonable)

            // if budget checkbox is ticked, budget from and budget to must have value
            if (this.formData['chkBudget'] == true && this.formData['budgetFrom'] == "" || this.formData['budgetTo'] == "") {
                alert("Please fill in both Budget From and Budget To");
                return;
            }

            // budget from must smaller than budget to
            if (this.formData['chkBudget'] == true && this.formData['budgetFrom'] > this.formData['budgetTo']) {
                alert("Budget From should be smaller or equal to Budget To");
                return;
            }

            this.submitEnquiry();
        },

        showPrinciplesSummary() {
            // alert("showPrinciplesSummary()");

            // Features:
            // 1. prepare chart data
            // 2. define chart options for stacked bar chart
            // 3. show legend, to indicate which color for which principle
            // 4. y-axis, show AE principle name
            // 5. Hide percentage of each bar (center principle name but number goes to top location...)
            // 6. show legend in separate container

            // ======================================== //

            var record = [];
            var index;


            // yours principles summary sorted
            var chart1Data0 = [];
            var chart1Data1 = [];
            var chart1Data2 = [];

            for (var i = this.yoursPrinciplesSummarySorted.length - 1; i >= 0; i--) {
                index = 13 - i;

                if (this.yoursPrinciplesSummarySorted[i].green != 0) {
                    record = [this.yoursPrinciplesSummarySorted[i].green, index];
                    chart1Data0.push(record);
                }

                record = [this.yoursPrinciplesSummarySorted[i].yellow, index];
                chart1Data1.push(record);

                if (this.yoursPrinciplesSummarySorted[i].red != 0) {
                    record = [this.yoursPrinciplesSummarySorted[i].red, index];
                    chart1Data2.push(record);
                }
            }


            // others principles summary sorted
            var chart2Data0 = [];
            var chart2Data1 = [];
            var chart2Data2 = [];

            for (var i = this.othersPrinciplesSummarySorted.length - 1; i >= 0; i--) {
                index = 13 - i;

                if (this.othersPrinciplesSummarySorted[i].green != 0) {
                    record = [this.othersPrinciplesSummarySorted[i].green, index];
                    chart2Data0.push(record);
                }

                record = [this.othersPrinciplesSummarySorted[i].yellow, index];
                chart2Data1.push(record);

                if (this.othersPrinciplesSummarySorted[i].red != 0) {
                    record = [this.othersPrinciplesSummarySorted[i].red, index];
                    chart2Data2.push(record);
                }
            }


            // prepare chart 1 and chart 2 data
            var chart1Data = [];
            chart1Data[0] = {label: '1.5 - 2', color: '#54C45E', data: chart1Data0};
            chart1Data[1] = {label: '0.5 - 1.5', color: '#FFE342', data: chart1Data1};
            chart1Data[2] = {label: '0 - 0.5', color: '#FF0000', data: chart1Data2};

            var chart2Data = [];
            chart2Data[0] = {label: '1.5 - 2', color: '#54C45E', data: chart2Data0};
            chart2Data[1] = {label: '0.5 - 1.5', color: '#FFE342', data: chart2Data1};
            chart2Data[2] = {label: '0 - 0.5', color: '#FF0000', data: chart2Data2};

            // prepare chart 1 and chart 2 y axis ticks
            var chart1yAxisTicks = [];

            for (var i = 0; i < this.yoursPrinciplesSummarySorted.length; i++) {
                var index = 13 - i;
                chart1yAxisTicks.push([index, this.yoursPrinciplesSummarySorted[i].name]);
            }

            var chart2yAxisTicks = [
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
            var chart1LegendFlag = false;
            var chart2LegendFlag = true;

            // prepare chart 1 and chart 2 Y axis label width
            var chart1YAxisLabelWidth = 250;
            var chart2YAxisLabelWidth = 0;


            // plot chart 1 with chart 1 data and options
            this.plotWithOptions("#chart1", chart1Data, chart1yAxisTicks, chart1YAxisLabelWidth, chart1LegendFlag, "");

            // plot chart 2 with chart 2 data and options
            this.plotWithOptions("#chart2", chart2Data, chart2yAxisTicks, chart2YAxisLabelWidth, chart2LegendFlag, "chart2Legend");
        },

        // chart options
        plotWithOptions(chartId, chartData, yAxisTicks, yAxisLabelWidth, legendFlag, legendId) {
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
        },

        submitEnquiry() {
            // alert("submitEnquiry()");

            // reset variables
            // this.status = null;
            // this.message = null;
            // this.statusSummary = null;
            // this.redlinesSummary = null;
            // this.yoursPrinciplesSummarySorted = null;
            // this.othersPrinciplesSummarySorted = null;

            // send ajax request to Controller
            axios.post("/admin/generic-dashboard/enquire", this.formData)

                .then(res => {
                    this.enquireResult = res.data;

                    this.status = this.enquireResult['status'];
                    this.message = this.enquireResult['message'];
                    this.statusSummary = this.enquireResult['statusSummary'];
                    this.redlinesSummary = this.enquireResult['redlinesSummary'];
                    this.yoursPrinciplesSummarySorted = this.enquireResult['yoursPrinciplesSummarySorted'];
                    this.othersPrinciplesSummarySorted = this.enquireResult['othersPrinciplesSummarySorted'];

                    if (this.status != "0") {
                        alert(this.message);
                    } else {
                        // show status summary automatically by data driven approach and v-if

                        // show red lines summary automatically by data driven approach and v-if

                        // show principles summary by calling Javascript function
                        window.setTimeout(this.showPrinciplesSummary, 100);
                    }

                })
                .catch(error => {

                    if (error.response) {
                        if (error.response.status === 401) {

                        }
                    }
                })

        },

    }
}

</script>
