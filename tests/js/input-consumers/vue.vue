<script setup lang="ts">
import { ref } from 'vue'
import Input from '../../../packages/support/resources/js/vue/Input.vue'
const value = ref<string | number>(0)
const input = ref<InstanceType<typeof Input>>()
type Props = InstanceType<typeof Input>['$props']
// @ts-expect-error Inline flags are boolean.
const invalid: Props = { inlinePrefix: 'yes' }
void invalid
</script>
<template>
    <Input
        ref="input"
        v-model.number.trim="value"
        type="number"
        :defaultValue="0"
        inline-prefix
        readonly
        @input="(event) => event.target"
        @focus="input?.element?.checkValidity()"
    />
</template>
