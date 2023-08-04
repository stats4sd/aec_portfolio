<template>
    <div class="collapse" :id="targetId">
    <div class="bd-callout border-info">
        {{ helpTextEntry.text }}
    </div>
</div>
</template>

<script setup>
import slugify from 'slugify';
import {onMounted, ref, computed} from "vue";

const props = defineProps({
    location: String,
})

const targetId = computed(() => {
    return slugify(props.location)
})

const helpTextEntry = ref({})



onMounted(() => {
    axios.get('/admin/help-text-entry/find/' + props.location)
        .then((res) => {
            helpTextEntry.value = res.data
        })
})

</script>
