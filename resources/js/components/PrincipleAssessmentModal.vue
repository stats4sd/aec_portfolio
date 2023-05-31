<template>

    <teleport to="body">
        <div class="vue-modal" v-if="isOpen" @click="isOpen = false">
            <div class="card rounded-0 px-5 py-2 d-flex flex-column justify-content-between">
                <div class="card-header">
                    <h2 class="card-title text-bright-green">{{ principle.name }}</h2>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>RATING</h4>
                            <small>Drag the slider to choose the rating for {{ principle.name }}. Refer to the definitions below if you are unsure.</small>


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
    </teleport>

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
    position: absolute;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.1);
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.vue-modal > div {
    background-color: #fff;
    width: 80%;
    height: 60%;
    max-width: 1200px;
}

</style>
