<script setup lang="ts">
import { ref } from 'vue'
import type { InputHTMLAttributes } from 'vue'

interface InputAttributes extends Omit<
    InputHTMLAttributes,
    'innerHTML' | 'textContent' | 'autocomplete' | 'value'
> {
    autocomplete?: string
    value?: string | number
    defaultValue?: string | number
}

interface InputProps extends /* @vue-ignore */ InputAttributes {
    inlinePrefix?: boolean
    inlineSuffix?: boolean
    modelValue?: string | number
}

defineOptions({ inheritAttrs: false })
withDefaults(defineProps<InputProps>(), {
    inlinePrefix: false,
    inlineSuffix: false,
})
const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()
const element = ref<HTMLInputElement>()
defineExpose({ element })
defineSlots<{}>()
</script>

<template>
    <input
        ref="element"
        @input="
            emit('update:modelValue', ($event.target as HTMLInputElement).value)
        "
        v-bind="{
            ...$attrs,
            ...(modelValue === undefined ? {} : { value: modelValue }),
        }"
        :class="[
            'fi-input',
            inlinePrefix && 'fi-input-has-inline-prefix',
            inlineSuffix && 'fi-input-has-inline-suffix',
        ]"
    />
</template>
