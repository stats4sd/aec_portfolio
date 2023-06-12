    <template>


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
                <h4 class="mb-4">COMMNENTS AND EXAMPLES</h4>

                <div>
                    <v-textarea
                        v-model="principleAssessment.rating_comment"
                        label="Add a comment to support your rating"
                    ></v-textarea>
                </div>

                <div>
                    <h6>Presence of Examples / Indicators for {{ principle.name }}</h6>
                    <p>Below are some common examples of {{ principle.name }} within a project. Tick the ones that are present within the project. You may also add additional examples below to further support the rating given.</p>

                    <div v-for="tag in principle.score_tags" class="checkbox-group">
                        <v-checkbox
                            v-model="principleAssessment.score_tag_ids"
                            :value="tag.id"
                            :label="tag.name"
                            density="compact"
                            hide-details="auto"
                        ></v-checkbox>
                    </div>
                </div>

                <div class="mt-8">
                    <div v-for="(tag, index) in principleAssessment.custom_score_tags" class="d-flex form-group">

                        <v-text-field
                            ref="tagNameRefs"
                            v-model="tag.name"
                            label="Enter a descriptive name for the example / indicator"
                            density="compact"
                            variant="underlined"
                            append-icon="mdi-delete"
                            @click:append="removeCustomScoreTag(index)"
                        />
                    </div>

                    <button class="btn btn-secondary" @click="addCustomScoreTag">
                        <i class="la la-plus"></i> Add New Example
                    </button>
                </div>

            </div>

        </div>
    </v-card-text>
    <div class="card-footer d-flex justify-content-between" style="margin-left: 25%; margin-right: 25%">
        <div class="btn btn-secondary" @click="discard">Discard Changes</div>
        <div class="btn btn-primary" @click="save('close')">Save and Close</div>
        <div class="btn btn-success" @click="save('next')">Save and Next</div>
    </div>

</template>

<script setup>
import {computed, ref, defineEmits, nextTick} from "vue";


const props = defineProps({
    principleAssessment: Object,
    isOpen: Boolean,
})

const principle = computed(() => props.principleAssessment.principle ?? null)
const assessment = computed(() => props.principleAssessment.assessment ?? null)
const has_rating = computed(() => props.principleAssessment.rating !== null || props.principleAssessment.is_na)

// score tags
const tagNameRefs = ref([])
function addCustomScoreTag() {
    props.principleAssessment.custom_score_tags.push({
        name: '',
        description: '',
    })

    const index = props.principleAssessment.custom_score_tags.length - 1;

    nextTick(() => tagNameRefs.value[index].focus())
}

function removeCustomScoreTag(index) {
    console.log('hi', index)
    props.principleAssessment.custom_score_tags.splice(index, 1)
}

// data handling

const emit = defineEmits(['discard', 'next', 'close'])
function discard() {
    emit('discard')
    emit('close')
}

async function save(nextAction) {

    props.principleAssessment.complete = has_rating;

    const res = await axios.patch(`/principle-assessment/${props.principleAssessment.id}`, props.principleAssessment)

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

</style>
