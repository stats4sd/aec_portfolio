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
                                v-model="selectedRegions"
                                :options="regions"
                                :reduce="region => region.id"
                                label="name"
                                placeholder="SELECT REGIONS"
                                multiple="true"
                                :clearable="true"
                            />

                            <b>COUNTRIES:</b>
                            <v-select
                                v-model="selectedCountries"
                                class="bg-white"
                                :options="filteredCountries"
                                :reduce="country => country.id"
                                label="name"
                                placeholder="SELECT COUNTRIES"
                                multiple="true"
                                :clearable="true"
                            />
                        </div>
                        <div class="col-lg-6 col-12 px-12">
                            <h4 class="mb-4">Filter by Start Date</h4>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pr-16">
                                    <b>FROM:</b>
                                    <vue-date-picker
                                        v-model="startDate"
                                        year-picker
                                        auto-apply
                                        text-input
                                    />
                                </div>
                                <div>
                                    <b>TO:</b>
                                    <vue-date-picker
                                        v-model="endDate"
                                        year-picker
                                        auto-apply
                                        text-input
                                    />
                                </div>
                            </div>
                            <hr/>
                            <h4 class="mt-8 mb-4">Filter by Initiative Budget</h4>
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <div class="pr-16">
                                    <b>MINIMUM BUDGET:</b>
                                    <input
                                        class="bg-white d-block py-1"
                                        v-model="minBudget"
                                    />
                                </div>
                                <div class="w-50">
                                    <b>MAXIMUM BUDGET:</b>
                                    <input
                                        class="bg-white d-block py-1"
                                        v-model="maxBudget"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-expand-transition>
        <div class="card-footer bg-blue-lighten-4 d-flex justify-content-between align-items-center px-12 font-lg">
            <div>
                <b class="pr-3">Filters:</b>
                <span class="text-dark pr-8">
                    NO FILTERS APPLIED
                </span>
            </div>
            <div class="btn btn-primary" @click="showFilters = !showFilters">{{ showFilters ? 'Apply' : 'Modify' }} Filters</div>
        </div>
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

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import {ref, computed, onMounted} from "vue";


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

const selectedRegions = ref([])
const selectedCountries = ref([])
const startDate = ref(null)
const endDate = ref(null)
const minBudget = ref(null)
const maxBudget = ref(null)

const filteredCountries = computed(() => {
    return props.countries.filter(country => selectedRegions.value.includes(country.region_id))
})


// TEST
onMounted(() => {
    showFilters.value = true;
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
</style>
