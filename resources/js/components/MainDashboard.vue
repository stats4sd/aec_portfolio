<template>


    <div
        class="card bg-light"
        :class="showFilters ? 'border-info' : ''"
    >
        <div class="card-body font-lg d-flex justify-content-between align-items-center px-12">
            <div style="min-width: 400px" class="d-flex align-items-center">
                <b class="pr-3">Showing:</b>
                <vSelect
                    class="flex-grow-1"
                    :options="portfolios"
                    :reduce="portfolio => portfolio.id"
                    :label="'name'"
                    placeholder="ALL PORTFOLIOS"
                    :clearable="true"
                ></vSelect>
            </div>
            <div>
                <b class="pr-3">Filters:</b>
                <span class="text-secondary pr-8">
                    NO FILTERS APPLIED
                </span>
                <div class="btn btn-primary" @click="showFilters = !showFilters">Add Filters</div>
            </div>
        </div>
        <v-expand-transition>
            <div v-if="showFilters">
                <div class="card-body bg-">
                    <div class="row">
                        <div class="col-lg-6 col-12 px-12">
                            <h3>Geographic Filters</h3>
                            <v-select
                                :options="regions"
                                :reduce="region => region.id"
                                label="name"
                                placeholder="FILTER BY REGION"
                                :clearable="true"
                            />

                        </div>
                    </div>
                </div>
            </div>
        </v-expand-transition>
    </div>


    <div class="mt-8 card">
        <div class="card-header">
            <h2>Summary of Initiatives</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex font-xl">
                            <span class="w-50 text-right pr-4"># Initiatives Added</span>
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
                            <span class="w-50 text-right pr-4">OVERALL SCORE</span>
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


<script setup>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'
import {ref, computed} from "vue";


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

const showFilters = ref(false)


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
</style>
