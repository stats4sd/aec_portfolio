<template>
    <div class="card-header">

        <div class="d-flex align-items-center">
            <h2 class="mb-0">Institution Details</h2>
            <v-help-text-link class="font-2xl" location="My Institution - Details"/>
        </div>
        <p class="help-block">Add or edit the relevant information for the institution.</p>

        <v-help-text-entry location="My Institution - Details"/>

    </div>

    <div class="card-body">
        <div class="form-group row mt-12">
            <label for="input_name" class="col-sm-4 col-form-label text-right pr-2">Institution Name</label>
            <div class="col-sm-8">
                <input name="name"
                       class="form-control"
                       id="input_name"
                       v-model="institution.name"
                       :disabled="!canEdit"
                >
                <span class="text-danger emphasis show" role="alert" v-if="errors.hasOwnProperty('name')">
                        <strong v-for="error in errors.name">{{ error }}</strong><br/>
                    </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="input_currency" class="col-sm-4 col-form-label text-right pr-2">Main Currency (3-letter
                code)</label>
            <div class="col-sm-8 col-lg-4">
                <input
                    name="currency"
                    class="form-control"
                    id="input_currency"
                    v-model="institution.currency"
                    :disabled="!canEdit"
                >
                <span class="text-danger emphasis show" role="alert" v-if="errors.hasOwnProperty('currency')">
                            <strong v-for="error in errors.currency">{{ error }}</strong><br/>
                        </span>
                <small id="currencyHelp" class="form-text font-sm">This currency will be used for the summary
                    dashboard. All initiative budgets for your institution will be converted into this
                    currency.</small>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="additional_criteria_check"
                        name="has_additional_criteria"
                        v-model="institution.has_additional_criteria"
                        :value="1"
                        :disabled="!canEdit"
                    >
                    <label class="form-check-label" for="additional_criteria_check">
                        This Institution uses additional assessment criteria
                    </label>
                    <v-help-text-link location="My Institution - Additional Assessment Criteria" type="popover"/>
                    <span class="text-danger emphasis show" role="alert"
                          v-if="errors.hasOwnProperty('has_additional_criteria')">
                            <strong v-for="error in errors.has_additional_criteria">{{ error }}</strong><br/>
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label for="data_sharing" class="mt-12 col-md-4 col-form-label text-md-right">
                Data Sharing Agreement
            </label>

            <div class="col-md-8 mt-12">
                <div class="alert alert-info">
                    <p>The use of the Portfolio Funding Assessment Tool is based on the data sharing agreement made between your institution and the Agroecology Coalition. </p>
                </div>

                <p>
                    <a href="/documents/Data Sharing Terms and Conditions - Agroecology Coalition Funding Assessment Tool.pdf" target="_blank">Download a copy of the agreement for review.</a>
                </p>

                <span class="show"
                      v-if="!institution.agreement_signed_at">
                    <p>Please tick the box below as a digital signature to confirm you agree to this data sharing agreement. The signature will be recorded when you submit this form.</p>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="agreement"
                            name="agreement"
                            v-model="institution.agreement"
                            :value="1"
                            :disabled="!canEdit"
                        >
                        <label class="form-check-label" for="agreement">
                            On behalf of {{ institution.name }}, I agree to abide by the data sharing agreement.
                        </label>
                    </div>
                </span>
                <span class="show"
                      v-if="institution.agreement_signed_at">
                        <p><b>This agreement was digitally signed on behalf of {{ institution.name }} on {{ moment(institution.agreement_signed_at).utc().format('YYYY-MM-DD') }} by {{ institution.signee.name }}.</b></p>
                </span>

            </div>
        </div>
        <hr/>

        <div class="form-group row">
            <div class="col-sm-4">
                <h3>Optional Information</h3>
                <p>Adding additional information will help improve the analysis of anonymised results.</p>
            </div>
        </div>
        <hr/>

        <div class="form-group row">
            <label for="input_type" class="mt-12 col-sm-4 col-form-label text-right pr-2">Select the Institution
                Type</label>
            <div class="mt-12 col-sm-8 col-lg-4">
                <v-select
                    name="institution_type"
                    id="input_type"
                    v-model="institution.institution_type_id"
                    :options="institutionTypes"
                    label="name"
                    :reduce="institutionType => institutionType.id"
                    :disabled="!canEdit"

                />
                <span class="text-danger emphasis show" role="alert"
                      v-if="errors.hasOwnProperty('institution_type_id')">
                            <strong v-for="error in errors.institution_type_id">{{ error }}</strong><br/>
                        </span>
                <small id="institution_type_help" class="form-text font-sm">This can be used as an additional
                    disaggregation for
                    analysis of funding flows.</small>
            </div>
        </div>

        <div class="form-group row">
            <label for="input_institution_type_other" class="col-sm-4 col-form-label text-right pr-2">Enter the
                'other' institution type:</label>
            <div class="col-sm-8 col-lg-4">
                <input name="institution_type_other"
                       class="form-control"
                       id="institution_type_other"
                       v-model="institution.institution_type_other"
                       :disabled="institution.institution_type_id !== 5 || !canEdit"
                >
                <span class="text-danger emphasis show" role="alert"
                      v-if="errors.hasOwnProperty('institution_type_other')">
                        <strong v-for="error in errors.institution_type_other">{{ error }}</strong><br/>
                    </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="input_geographic_reach" class="col-sm-4 col-form-label text-right pr-2">What is the
                geographic reach of the institution?</label>
            <div class="col-sm-8 col-lg-4">
                <v-select
                    name="geographic_reach"
                    id="input_geographic_reach"
                    v-model="institution.geographic_reach"
                    :options="geographicReaches"
                    :disabled="!canEdit"
                />
                <span class="text-danger emphasis show" role="alert"
                      v-if="errors.hasOwnProperty('geographic_reach')">
                            <strong v-for="error in errors.geographic_reach">{{ error }}</strong><br/>
                    </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="input_hq_country" class="col-sm-4 col-form-label text-right pr-2">What country is the
                institution HQ based?</label>
            <div class="col-sm-8 col-lg-4">
                <v-select
                    name="hq_country"
                    id="input_hq_country"
                    :options="countries"
                    label="name"
                    :reduce="country => country.id"
                    v-model="institution.hq_country"
                    :disabled="!canEdit"
                />
                <span class="text-danger emphasis show" role="alert" v-if="errors.hasOwnProperty('hq_country')">
                            <strong v-for="error in errors.hq_country">{{ error }}</strong><br/>
                        </span>
            </div>
        </div>

        <div class="form-group d-flex justify-content-end mt-16"
             v-if="canEdit">
            <button type="cancel" class="btn btn-secondary mr-4">Discard Changes</button>
            <button class="btn btn-primary" @click="save">Save</button>
        </div>

    </div>

</template>

<script setup>

import {onMounted, ref, computed} from "vue";
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'
import moment from "moment";
import VHelpTextEntry from "./vHelpTextEntry.vue";
import VHelpTextLink from "./vHelpTextLink.vue";


const props = defineProps({
    initInstitution: Object,
    institutionTypes: Array,
    geographicReaches: Array,
    countries: Array,
    updateRoute: String,
    user: Object,
})

const errors = ref({})
const institution = ref({});

const canEdit = computed(() => props.user.permission_names.includes('edit own institution'))


onMounted(() => {
    institution.value = {...props.initInstitution}
    criteria.value = institution.value.has_additional_criteria
})

async function save() {

    try {
        const result = await axios.post(props.updateRoute, institution.value)

        institution.value = result.data

        if (criteria.value !== institution.value.has_additional_criteria) {
            window.location = 'saved'
        } else {

            new Noty({
                type: "success",
                text: "The Institution data was successfully saved"
            }).show();
        }
    } catch (error) {
        errors.value = error.response?.data.errors ?? {}
    }

}

// handle reload of page if additional assessment criteria is changed
const criteria = ref(false)


</script>
