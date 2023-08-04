<template>
    <div class="collapse" :id="sectionId">
    <div class="bd-callout border-info">
        {{ helpTextEntry.text }}
    </div>
</div>
</template>

<script setup>

import {onMounted, ref} from "vue";

const props = defineProps({
    sectionId: String,
    location: String,
})

const helpTextEntry = ref({})

onMounted(() => {
    axios.get('/admin/help-text-entry/find/' + props.location)
        .then((res) => {
            helpTextEntry.value = res.data
        })
})

</script>
