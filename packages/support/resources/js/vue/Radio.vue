<script setup lang="ts">
import type { InputHTMLAttributes } from 'vue'

interface RadioInputAttributes extends Omit<
    InputHTMLAttributes,
    'type' | 'innerHTML' | 'textContent' | 'autocomplete' | 'value'
> {
    // Avoid expanding Vue's `autocomplete` token union in `withDefaults()`.
    autocomplete?: string
    defaultChecked?: boolean
}

interface RadioProps extends /* @vue-ignore */ RadioInputAttributes {
    valid?: boolean
    value?: string
    modelValue?: string | null
}

defineOptions({ inheritAttrs: false })
withDefaults(defineProps<RadioProps>(), {
    valid: true,
    value: 'on',
    modelValue: undefined,
})
const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()
defineSlots<{}>()
</script>

<template>
    <input
        @change="
            emit('update:modelValue', ($event.target as HTMLInputElement).value)
        "
        v-bind="{
            ...$attrs,
            ...(modelValue === undefined
                ? {}
                : { checked: modelValue === value }),
        }"
        type="radio"
        :value="value"
        :class="['fi-radio-input', !valid && 'fi-invalid']"
    />
</template>
