<script setup lang="ts">
import { ref } from 'vue'
import LoadingSection from '../../../packages/support/resources/js/vue/LoadingSection.vue'
const element = ref<InstanceType<typeof LoadingSection>>()
// @ts-expect-error `height` is a CSS length, not a pixel count.
const invalid: InstanceType<typeof LoadingSection>['$props'] = { height: 12 }
void invalid
</script>
<template>
    <LoadingSection
        ref="element"
        :column-span="{ default: 'full', lg: 2 }"
        :column-start="3"
        height="12rem"
        loading-label="Loading projects"
        title="Projects"
        @click="(event) => event.currentTarget"
    />
</template>
