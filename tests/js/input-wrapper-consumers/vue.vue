<script setup lang="ts">
import InputWrapper from '../../../packages/support/resources/js/vue/InputWrapper.vue'
import Icon from '../../../packages/support/resources/js/vue/Icon.vue'
type Props = InstanceType<typeof InputWrapper>['$props']
// @ts-expect-error Wrapper state is boolean.
const invalid: Props = { valid: 'false' }
void invalid
</script>
<template>
    <InputWrapper
        disabled
        :valid="false"
        inline-prefix
        prefix="£"
        @click="(event) => event.currentTarget"
    >
        <template #prefixIcon><Icon src="/currency.svg" /></template>
        <template #suffix><strong>GBP</strong></template>
        <template #suffixActions><button type="button">Clear</button></template>
        <input required />
    </InputWrapper>
</template>
