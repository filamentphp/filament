<script setup lang="ts">
import Fieldset from '../../../packages/support/resources/js/vue/Fieldset.vue'
type Props = InstanceType<typeof Fieldset>['$props']
// @ts-expect-error `contained` must be a boolean.
const invalidContained: Props = { contained: 'false' }
// @ts-expect-error `disabled` must use Vue's native boolean attribute type.
const invalidDisabled: Props = { disabled: 42 }
void invalidContained
void invalidDisabled
</script>
<template>
    <Fieldset
        label="Address"
        required
        label-hidden
        :contained="false"
        disabled
        name="address"
        form="profile"
        @click="(event) => event.preventDefault()"
    >
        <template #label><strong>Address</strong></template>
        <input aria-label="Street" />
    </Fieldset>
</template>
