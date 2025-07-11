<template>

    <Transition mode="out-in" name="fade">
        <v-card :key="principleAssessment">
            <v-card-title class="px-0">
                <h2 class="card-title text-bright-green pt-8 pb-0 px-12">{{ principle.name }}</h2>
            </v-card-title>

            <v-card-text class="px-0">
                <div class="h-100 d-flex">
                    <div class="col-md-6 px-12 py-4 h-100">
                        <div>
                            <h4>RATING</h4>
                            <p class="pb-8 mb-4">Drag the slider to choose the rating for {{ principle.name }}. Refer to the definitions below if you are unsure.</p>
                            <v-slider
                                v-model="principleAssessment.rating"
                                :min="0"
                                :max="2"
                                :step="0.1"
                                :thumb-size="has_rating ? 18 : 24"
                                :thumb-label="has_rating ? 'always' : 'never'"
                                :thumb-color="has_rating ? 'green' : 'grey'"
                                track-color="light"
                                track-fill-color="success"
                                show-ticks="always"
                                class="ae-slider"
                                :disabled="principleAssessment.is_na"
                            >
                                <template #thumb-label="data">
                                    {{ has_rating ? principleAssessment.rating : '' }}
                                </template>
                                <template #tick-label="data">
                                    {{ data.tick.value % 1 === 0 ? data.tick.value : '' }}
                                </template>
                            </v-slider>

                            <v-checkbox
                                v-if="principle.can_be_na==1"
                                class="my-4"
                                v-model="principleAssessment.is_na"
                                :label="`If ${principle.name.toLowerCase()} is not applicable for this project, tick this box.`">
                            </v-checkbox>
                            <p class="alert alert-info" v-if="principleAssessment.is_na">You may still add a comment to explain why {{ principle.name }} is not applicable for this project.</p>
                        </div>

                        <div class="card bg-bright-green text-white rounded-b-xl rounded-t-xl mb-16">
                            <div class="card-body p-4 px-8">
                                <h3 class="card-title">Definitions</h3>

                                <table class="align-text-top">
                                    <tr>
                                        <td class="align-text-top pr-4 pb-4">2</td>
                                        <td class="align-text-top pr-4 pb-4">{{ principle.rating_two }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-text-top pr-4 pb-4">1</td>
                                        <td class="align-text-top pr-4 pb-4">{{ principle.rating_one }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-text-top pr-4 pb-4">0</td>
                                        <td class="align-text-top pr-4 pb-4">{{ principle.rating_zero }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-text-top pr-4 pb-4">N/A</td>
                                        <td class="align-text-top pr-4 pb-4">{{ principle.rating_na }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>


                    </div>

                    <div class="col-md-6 px-12 py-4 h-100">
                        <h4 class="mb-4">COMMENTS AND EXAMPLES</h4>

                        <div>
                            <v-textarea
                                v-model="principleAssessment.rating_comment"
                                label="Add a comment to support your rating"
                            ></v-textarea>
                        </div>

                        <div>
                            <h5>Presence of Examples // Indicators
                                <br/>for {{ principle.name }}
                            </h5>
                            <p>Below are some common examples of {{ principle.name }} within a project. Tick the ones that are present within the project. You may also add additional examples below to further support the rating given.</p>

                            <div v-for="tag in principle.score_tags" class="checkbox-group mb-2 example-list">
                                <v-checkbox
                                    v-model="principleAssessment.score_tag_ids"
                                    :value="tag.id"
                                    :label="tag.name"
                                    density="compact"
                                    hide-details="auto"
                                ></v-checkbox>
                            </div>
                            <div v-for="(tag, index) in principleAssessment.custom_score_tags" class="d-flex">
                                <v-text-field
                                    ref="tagNameRefs"
                                    v-model="tag.name"
                                    density="compact"
                                    variant="underlined"
                                    prepend-icon="mdi-check"
                                    append-icon="mdi-delete"
                                    @click:append="removeCustomScoreTag(index)"
                                />
                            </div>


                            <div>
                                <p>
                                    <b>Add new examples</b><br/>Type to add your own custom examples to support your rating. Click the + button or press Enter to add an example. You may add as many as you wish.
                                </p>

                                <div class="d-flex form-group">

                                    <v-text-field
                                        ref="newTagRef"
                                        v-model="newCustomTag"
                                        label="Enter a descriptive name for the example / indicator"
                                        density="compact"
                                        variant="underlined"
                                        append-icon="mdi-plus-circle"
                                        @click:append="addCustomScoreTag"
                                        @keydown.enter="addCustomScoreTag"
                                    />
                                </div>

                            </div>


                        </div>

                    </div>

                </div>
            </v-card-text>
            <div class="card-footer d-flex justify-content-between" style="margin-left: 25%; margin-right: 25%">
                <div class="btn btn-secondary" @click="discard">Discard Changes</div>
                <div class="btn btn-primary" @click="save('close')">Save and Close</div>
                <div class="btn btn-success" @click="save('next')">Save and Next</div>
            </div>

        </v-card>
    </Transition>

</template>

<script setup>
import {computed, ref, defineEmits, nextTick, watch} from "vue";


const props = defineProps({
    principleAssessment: Object,
    assessmentType: String,
    closing: Boolean,
})

const principle = computed(() => props.principleAssessment.principle ?? null)
const assessment = computed(() => props.principleAssessment.assessment ?? null)
const has_rating = computed(() => props.principleAssessment.rating !== null || props.principleAssessment.is_na)

// for user to enter new score tag
const newCustomTag = ref('')
const newTagRef = ref('');

// score tags
const tagNameRefs = ref([])

function addCustomScoreTag() {
    props.principleAssessment.custom_score_tags.push({
        name: newCustomTag.value,
        description: '',
    })

    newCustomTag.value = '';

    nextTick(() => {
        newTagRef.value.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        })
    })

}

function removeCustomScoreTag(index) {
    console.log('hi', index)
    props.principleAssessment.custom_score_tags.splice(index, 1)
}

// data handling

const emit = defineEmits(['discard', 'next', 'close', 'update_rating'])

function discard() {
    emit('discard')
    emit('close')
}

async function save(nextAction) {

    // slider appears to be '0' on null, but actually requires moving up and back to 0 to be considered not null.
    if (props.principleAssessment.rating === null && !props.principleAssessment.is_na) {
        props.principleAssessment.rating = 0;
    }

    if (props.principleAssessment.is_na) {
        props.principleAssessment.rating = null;
    }

    props.principleAssessment.complete = true;

    emit('update_rating', props.principleAssessment)

    let url = `/principle-assessment/${props.principleAssessment.id}`

    // check if we are saving a principle-assessment or additional-criteria-assessment
    if (props.assessmentType === "additional") {
        url = `/additional-assessment/${props.principleAssessment.id}`
    }

    const res = await axios.patch(url, props.principleAssessment)

    emit(nextAction)

}


</script>


<style scoped>

.vue-modal {

    background-color: rgba(0, 0, 0, 0.1);
    width: 80vw;
    min-height: 85vh;
    display: flex;
    justify-content: center;
}

.vue-modal > div {
    background-color: #fff;
    width: 100%;
    max-width: 1200px;
}


.fade-enter-active, .fade-leave-active {
    transition: opacity .2s ease;
}

.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */
{
    opacity: 0;
}

</style>
