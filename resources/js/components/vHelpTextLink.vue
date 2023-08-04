<template>
    <i
        class="ml-2 cursor-help la la-question-circle"
        data-toggle="collapse"
        role="button"
        aria-expanded="false"
        :data-target="sectionId">
    </i>

</template>

<script setup>

const props = defineProps({
    sectionId: String,
});

</script>
