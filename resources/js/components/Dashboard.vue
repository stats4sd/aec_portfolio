<template>

    <div class="container pt-2">

    <table border="1" width="1000">

        <tr>
        <td>
            <table border="1">
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
                        <select name="projectStartFrom" id="projectStartFrom">
                            <option>2020</option>
                            <option>2021</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">To
                        <select name="projectStartTo" id="projectStartTo">
                            <option>2040</option>
                            <option>2041</option>
                        </select>
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
                        <select name="budgetFrom" id="budgetFrom">
                            <option>500,000</option>
                            <option>600,000</option>
                        </select>
                        EUR
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right">To
                        <select name="budgetTo" id="budgetTo">
                            <option>2,000,000</option>
                            <option>3,000,000</option>
                        </select>
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
