<script setup lang="ts">
import { ref } from 'vue'
import EmptyState from '../../../packages/support/resources/js/vue/EmptyState.vue'
const element = ref<InstanceType<typeof EmptyState>>()
type Props = InstanceType<typeof EmptyState>['$props']
// @ts-expect-error `headingTag` must be a heading element.
const invalid: Props = { headingTag: 'script' }
void invalid
</script>
<template>
    <EmptyState
        ref="element"
        heading="Projects"
        heading-tag="h3"
        compact
        :contained="false"
        icon-size="lg"
        icon-color="success"
        title="Projects"
        @click="(event) => event.currentTarget"
    >
        <template #heading><strong>Projects</strong></template>
        <template #description><em>Start here</em></template>
        <template #icon><svg /></template>
        <button>Create</button>
    </EmptyState>
</template>
