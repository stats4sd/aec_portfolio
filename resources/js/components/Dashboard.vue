<template>

    <div>
        Dashboard vue component
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
