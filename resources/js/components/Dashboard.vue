<template>

    <div>
        TODO
    </div>

</template>


<script>

import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

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
        vSelect
    },

    data() {
        return {

            // variables declaration
            userId: '',
            userOrganisation: '',

            // toast notification
            toast: useToast({position: 'bottom', duration: 4000}),
        }
    },

    // triggered when Vue component is loaded
    mounted() {
        // initialise variables once team object is accessible
        // this.teamId = this.team.id;
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
            this.arrayIndex = '';
            this.farm = null;
        },

    }
}

</script>
