<template>

    <div class="container pt-2">

    <!-- dashboard filters -->
    <!-- Question: How to have a better look and feel for table...? -->
    <table class="table table-bordered">

        <tr>
        <td>
            <table>
                <tr>
                    <td>Dashboard Filters</td>
                </tr>
                <tr>
                    <td>Select Portfolio</td>
                    <td>
                        <select v-model="formData['portfolio']">
                            <option value="0">All Portfolios</option>
                            <option :value="portfolio.id" v-for="portfolio in portfolios">{{ portfolio.name }}</option>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        </tr>

        <tr>
        <td>
            <table border="1">
                <tr>
                    <td colspan="2">Geographic Filters</td>
                    <td>Other Filters</td>
                </tr>
                <tr>
                    <td><input type="checkbox" v-model="formData['chkRegion']"> Filter by Region</td>
                    <td>
                        <select v-model="formData['region']">
                            <option :value="region.id" v-for="region in regions">{{ region.name }}</option>
                        </select>
                    </td>
                    <td><input type="checkbox" v-model="formData['chkProjectStart']"> Filter by Project Start</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">From
                        <input type="number" v-model="formData['projectStartFrom']" min="2020" max="2100" style="width:80px;">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">To
                        <input type="number" v-model="formData['projectStartTo']" min="2020" max="2100" style="width:80px;">
                    </td>
                </tr>

                <tr>
                    <td><input type="checkbox" v-model="formData['chkCountry']"> Filter by Country</td>
                    <td>
                        <select v-model="formData['country']">
                            <!-- TODO: When region change, remove selected country and show countries within the selected region -->                        
                            <!-- show all countries temporary for testing -->
                            <option :value="country.id" v-for="country in countries">{{ country.name }}</option>
                        </select>
                    </td>
                    <td><input type="checkbox" v-model="formData['chkBudget']"> Filter by Budget</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <!-- Question: How to show budget with thousand separator...? -->
                    <td align="right">From
                        <input type="number" v-model="formData['budgetFrom']" min="0" max="10000000" step="100000" style="width:100px;">
                        EUR
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">To
                        <input type="number" v-model="formData['budgetTo']" min="0" max="10000000" step="100000" style="width:100px;">
                        EUR
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td align="center">
                        <input type="hidden" v-model="formData['organisation']">
                        <button @click="validateCriteria" class="btn btn-primary">Submit</button>
                    </td>
                    <td></td>
                </tr>

            </table>
        </td>
        </tr>

    </table>



    <!-- status summary -->
    <table class="table" v-if="statusSummary != null">

        <thead>
            <th>-</th>
            <th>Number</th>
            <th>%</th>
            <th>Budget</th>
        </thead>

        <tbody>
        <tr v-for='statusSummaryRecord in statusSummary'>
            <td>{{ statusSummaryRecord.status }}</td>
            <td>{{ statusSummaryRecord.number }}</td>
            <td>{{ statusSummaryRecord.percent }}</td>
            <td>{{ statusSummaryRecord.budget }}</td>
        </tr>
        </tbody>

    </table>


    <!-- red lines summary -->
    <table class="table" v-if="redlinesSummary != null">

        <thead>
            <th></th>
            <th colspan="2">% projects passed</th>
        </thead>

        <thead>
            <th>Red Lines</th>
            <th>Yours</th>
            <th>Others</th>
        </thead>

        <tbody>
        <tr v-for='redlinesSummaryRecord in redlinesSummary'>
            <td :class="[ redlinesSummaryRecord.yours == 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.name }}</td>
            <td :class="[ redlinesSummaryRecord.yours == 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.yours }}%</td>
            <td :class="[ redlinesSummaryRecord.yours == 100 ? 'table-success' : 'table-warning' ]">{{ redlinesSummaryRecord.others }}%</td>
        </tr>
        </tbody>

    </table>

    
    <!-- principles summary: sort by -->
    <table class="table" v-if="yoursPrinciplesSummarySorted != null">
        <thead>
            <th width="30%">Principles</th>
            <th width="70%">
                <select v-model="formData['sortBy']" @change="validateCriteria">
                    <option value="1">Highest to Lowest</option>
                    <option value="2">Lowest to Highest</option>
                    <option value="3">Default</option>
                </select>
            </th>
        </thead>
    </table>


    <!-- principles summary -->
	<table class="table" v-if="yoursPrinciplesSummarySorted != null">
        <thead>
            <th width="30%"></th>
            <th width="30%" align="center"><u>Your Portfolio</u></th>
            <th width="30%"><center><u>Comparison Group</u></center></th>
            <th width="10%"></th>
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

    components: {

    },

    data() {
        return {
            // form variables
            formData: {},

            // enquire result
            enquireResult: null,
            status : '',
            message : '',
            statusSummary: null,
            redlinesSummary: null,
            yoursPrinciplesSummarySorted: null,
            othersPrinciplesSummarySorted: null,
        }
    },

    // triggered when Vue component is loaded
    mounted() {

        // temporary hard code to make testing quicker
        //this.formData['portfolio'] = 20;
        this.formData['sortBy'] = 1;

        // set default values
        this.formData['organisation'] = this.organisation.id;
        this.formData['projectStartFrom'] = '2020';
        this.formData['projectStartTo'] = '2030';
        this.formData['budgetFrom'] = '100000';
        this.formData['budgetTo'] = '10000000';
    },

    computed: {
        
    },

    watch: {
        // monitor the value of variable 

    },

    // custom methods for Vue component
    methods: {

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

                record = [this.yoursPrinciplesSummarySorted[i].green, index];
                chart1Data0.push(record);

                record = [this.yoursPrinciplesSummarySorted[i].yellow, index];
                chart1Data1.push(record);

                record = [this.yoursPrinciplesSummarySorted[i].red, index];
                chart1Data2.push(record);
            }


            // others principles summary sorted
            var chart2Data0 = [];
            var chart2Data1 = [];
            var chart2Data2 = [];

            for (var i = this.othersPrinciplesSummarySorted.length - 1; i >= 0; i--) {
                index = 13 - i;

                record = [this.othersPrinciplesSummarySorted[i].green, index];
                chart2Data0.push(record);

                record = [this.othersPrinciplesSummarySorted[i].yellow, index];
                chart2Data1.push(record);

                record = [this.othersPrinciplesSummarySorted[i].red, index];
                chart2Data2.push(record);
            }


            // prepare chart 1 and chart 2 data
            var chart1Data = [];
            chart1Data[0] = { label: '1.5 - 2',   color: '#54C45E', data: chart1Data0 };
            chart1Data[1] = { label: '0.5 - 1.5', color: '#FFE342', data: chart1Data1 };
            chart1Data[2] = { label: '0 - 0.5',   color: '#FF0000', data: chart1Data2 };

            var chart2Data = [];
            chart2Data[0] = { label: '1.5 - 2',   color: '#54C45E', data: chart2Data0 };
            chart2Data[1] = { label: '0.5 - 1.5', color: '#FFE342', data: chart2Data1 };
            chart2Data[2] = { label: '0 - 0.5',   color: '#FF0000', data: chart2Data2 };

            // prepare chart 1 and chart 2 y axis ticks
            var chart1yAxisTicks = [];
            
            for (var i=0; i < this.yoursPrinciplesSummarySorted.length; i++) {
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

            // plot chart 1 with chart 1 data and options
            this.plotWithOptions("#chart1", chart1Data, chart1yAxisTicks, chart1LegendFlag, "");
            
            // plot chart 2 with chart 2 data and options
            this.plotWithOptions("#chart2", chart2Data, chart2yAxisTicks, chart2LegendFlag, "chart2Legend");
        },

        // chart options
        plotWithOptions(chartId, chartData, yAxisTicks, legendFlag, legendId) {
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
                        numbers: { show: false }
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

            .then(res =>{
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
            .catch(error=>{

                if(error.response) {
                    if(error.response.status === 401) {

                    }
                }
            })

        },

    }
}

</script>
