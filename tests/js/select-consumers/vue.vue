<script setup lang="ts">
import { ref } from 'vue'
import Select from '../../../packages/support/resources/js/vue/Select.vue'
const single = ref('drawing')
const multiple = ref(['drawing'])
const objects = ref(new Set([{ id: 1 }]))
const element = ref<{ element?: HTMLSelectElement }>()
</script>
<template>
    <Select
        ref="element"
        v-model="single"
        inline-prefix
        @change="(event) => event.target"
        @focus="element?.element?.checkValidity()"
        ><option value="drawing">Drawing</option></Select
    >
    <Select v-model="multiple" multiple
        ><option value="drawing">Drawing</option></Select
    >
    <Select v-model="objects" multiple
        ><option :value="{ id: 1 }">Drawing</option></Select
    >
    <!-- @vue-expect-error Inline flags are boolean. -->
    <Select inline-prefix="yes"><option>Invalid</option></Select>
</template>
