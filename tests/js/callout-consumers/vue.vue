<script setup lang="ts">
import { ref } from 'vue'
import Callout from '../../../packages/support/resources/js/vue/Callout.vue'
const element = ref<InstanceType<typeof Callout>>()
// @ts-expect-error `iconSize` must be a supported size.
const invalid: InstanceType<typeof Callout>['$props'] = { iconSize: 'huge' }
void invalid
</script>
<template>
    <Callout
        ref="element"
        color="brand"
        description="Review"
        icon-color="gray"
        icon-size="sm"
        title="Notice"
        @click="(event) => event.currentTarget"
    >
        <template #heading><strong>Notice</strong></template>
        <template #icon><svg /></template>
        <template #controls><button>Help</button></template>
        <button>Continue</button>
    </Callout>
</template>
