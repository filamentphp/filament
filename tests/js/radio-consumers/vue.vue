<script setup lang="ts">
import { ref } from 'vue'
import Radio from '../../../packages/support/resources/js/vue/Radio.vue'
const delivery = ref<string | null>(null)
type Props = InstanceType<typeof Radio>['$props']
// @ts-expect-error The primitive is always a radio.
const invalidType: Props = { type: 'checkbox' }
// @ts-expect-error Raw markup is unsupported.
const invalidHtml: Props = { innerHTML: '' }
// @ts-expect-error Models use native string values, not objects.
const invalidModel: Props = { modelValue: {} }
void invalidType
void invalidHtml
void invalidModel
</script>
<template>
    <Radio
        v-model="delivery"
        value="express"
        name="delivery"
        :valid="false"
        required
        form="order"
        @change="(event) => event.preventDefault()"
    />
    <Radio :defaultChecked="true" disabled autocomplete="off" />
</template>
