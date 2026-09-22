<script setup lang="ts">
import LoadingIndicator from '../../../packages/support/resources/js/vue/LoadingIndicator.vue'
type Props = InstanceType<typeof LoadingIndicator>['$props']
// @ts-expect-error Sizes match `IconSize`.
const invalidSize: Props = { size: 'huge' }
// @ts-expect-error Raw markup is unsupported.
const invalidHtml: Props = { innerHTML: '' }
void invalidSize
void invalidHtml
</script>
<template>
    <LoadingIndicator
        size="xs"
        :aria-hidden="false"
        role="img"
        aria-label="Loading"
        viewBox="0 0 32 32"
        @click="(event) => event.preventDefault()"
    />
</template>
