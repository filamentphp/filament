<script setup lang="ts">
import { ref } from 'vue'
import Actions from '../../../packages/support/resources/js/vue/Actions.vue'
const element = ref<InstanceType<typeof Actions>>()
// @ts-expect-error `fullWidth` must be boolean.
const invalid: InstanceType<typeof Actions>['$props'] = { fullWidth: 'yes' }
void invalid
</script>
<template>
    <Actions
        ref="element"
        alignment="between"
        full-width
        title="Actions"
        @click="(event) => event.currentTarget"
        ><button type="submit">Save</button></Actions
    >
</template>
