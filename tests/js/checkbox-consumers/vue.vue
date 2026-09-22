<script setup lang="ts">
import { ref } from 'vue'
import Checkbox from '../../../packages/support/resources/js/vue/Checkbox.vue'
const checked = ref(false)
type Props = InstanceType<typeof Checkbox>['$props']
// @ts-expect-error The primitive is always a checkbox.
const invalidType: Props = { type: 'radio' }
// @ts-expect-error Raw markup is unsupported.
const invalidHtml: Props = { innerHTML: '' }
// @ts-expect-error The model is boolean, not a collection.
const invalidModel: Props = { modelValue: [] }
void invalidType
void invalidHtml
void invalidModel
</script>
<template>
    <Checkbox
        v-model="checked"
        :valid="false"
        name="terms"
        value="accepted"
        required
        form="profile"
        @change="(event) => event.preventDefault()"
    />
    <Checkbox :defaultChecked="true" :indeterminate="true" autocomplete="off" />
</template>
