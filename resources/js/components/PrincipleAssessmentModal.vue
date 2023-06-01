<template>

    <div class="vue-modal mr-auto ml-auto">
        <div class="card rounded-0 px-5 py-2 d-flex flex-column justify-content-between">
            <div class="card-header">
                <h2 class="card-title text-bright-green">{{ principle.name }}</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>RATING</h4>
                        <p class="pb-5">Drag the slider to choose the rating for {{ principle.name }}. Refer to the definitions below if you are unsure.</p>
                        <v-slider
                            v-model="principleAssessment.rating"
                            :min="0"
                            :max="2"
                            :step="0.1"
                            :thumb-size="24"
                            thumb-label="always"
                            thumb-color="green"
                            color="success"
                        ></v-slider>
                    </div>
                    <div class="col-md-6">

                    </div>

                </div>
            </div>
            <div class="card-footer d-flex justify-content-between" style="margin-left: 25%; margin-right: 25%">
                <div class="btn btn-secondary">Discard Changes</div>
                <div class="btn btn-primary">Save and Close</div>
                <div class="btn btn-success">Save and Next</div>
            </div>
        </div>
    </div>

</template>

<script setup>
import {computed} from "vue";


const props = defineProps({
    principleAssessment: Object,
    isOpen: Boolean,
})


const principle = computed(() => props.principleAssessment.principle ?? null)
const assessment = computed(() => props.principleAssessment.assessment ?? null)

</script>

<style scoped>

.vue-modal {

    background-color: rgba(0, 0, 0, 0.1);
    width: 80vw;
    height: 70vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.vue-modal > div {
    background-color: #fff;
    width: 100%;
    height: 100%;
    max-width: 1200px;
}

</style>
