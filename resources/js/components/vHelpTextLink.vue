<template>

    <i
        class="ml-2 cursor-help la la-question-circle"
        :data-toggle="type"
        :data-target="type==='collapse' ? '#'+targetId : ''"
        data-container="body"
        data-html="true"
        data-trigger="focus"
        tabindex="0"
        :data-content="helpTextEntry.text"
        role="button"
        aria-expanded="false"
        ref="popoverIcon"
    >
    </i>

</template>

<script setup>
import slugify from 'slugify';

import {computed, onMounted, ref} from "vue";

const props = defineProps({
    location: String,
    type: {
        type: String,
        default: 'collapse',
    }
});

const targetId = computed(() => {
    return slugify(props.location)
})


const helpTextEntry = ref({})
const popoverIcon = ref(null)

onMounted(() => {

    $(popoverIcon.value).popover();

    if (props.type === 'popover') {
        axios.get('/admin/help-text-entry/find/' + props.location)
            .then((res) => {
                helpTextEntry.value = res.data
            })
    }
})


</script>
