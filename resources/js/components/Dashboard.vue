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



    <!-- response status and message -->
    <table>
        <tr><td>
            <input type="text" v-model="status">
        </td></tr>
        <tr><td>
            <input type="text" v-model="message">
        </td></tr>
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


    <!-- principles summary -->




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
        // hard code to make testing quicker
        this.formData['portfolio'] = 20;

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

        submitEnquiry() {
            // alert("submitEnquiry()");

            // send ajax request to Controller
            axios.post("/admin/generic-dashboard/enquire", this.formData)

            .then(res =>{
                this.enquireResult = res.data;

                this.status = this.enquireResult['status'];
                this.message = this.enquireResult['message'];
                this.statusSummary = this.enquireResult['statusSummary'];
                this.redlinesSummary = this.enquireResult['redlinesSummary'];
                this.yoursPrinciplesSummarySorted = this.enquireResult['yoursPrinciplesSummarySorted'];
                this.othersPrinciplesSummarySorted = this.enquireResult['staothersPrinciplesSummarySortedtus'];

                // show status summary

                // show red lines summary

                // show principles summary

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
