<template>

    <div class="container pt-2">

    <!--
    <table border="1" width="1000">
    -->
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
                        <select name="portfolio" id="portfolio">
                            <option>Portfolio 1</option>
                            <option>Portfolio 2</option>
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
                    <td><input type="checkbox" name="chkRegion"> Filter by Region</td>
                    <td>
                        <select name="region" id="region">
                            <option>Region 1</option>
                            <option>Region 2</option>
                        </select>
                    </td>
                    <td><input type="checkbox" name="chkProjectStart"> Filter by Project Start</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">From
                        <input type="number" id="projectStartFrom" name="projectStartFrom" min="2020" max="2100" value="2020" style="width:80px;">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">To
                        <input type="number" id="projectStartTo" name="projectStartTo" min="2020" max="2100" value="2030" style="width:80px;">
                    </td>
                </tr>

                <tr>
                    <td><input type="checkbox" name="chkCountry"> Filter by Country</td>
                    <td>
                        <select name="country" id="country">
                            <option>Country 1</option>
                            <option>Country 2</option>
                        </select>
                    </td>
                    <td><input type="checkbox" name="chkProjectStart"> Filter by Budget</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">From
                        <input type="number" id="budgetFrom" name="budgetFrom" min="0" max="10000000" value="100000" step="100000" style="width:100px;">
                        EUR
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">To
                        <input type="number" id="budgetTo" name="budgetTo" min="0" max="10000000" value="10000000" step="100000" style="width:100px;">
                        EUR
                    </td>
                </tr>

            </table>
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
    },

    components: {

    },

    data() {
        return {

            // variables declaration
            userId: '',
            userOrganisation: '',
        }
    },

    // triggered when Vue component is loaded
    mounted() {
        // initialise variables
        this.userId = this.user.id;
    },

    computed: {
        
    },

    watch: {
        // monitor the value of variable 

    },

    // custom methods for Vue component
    methods: {

        // clear data entry form
        clearForm() {
            
        },

    }
}

</script>
