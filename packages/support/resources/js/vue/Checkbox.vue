<script setup lang="ts">
import type { InputHTMLAttributes } from 'vue'

interface CheckboxInputAttributes extends Omit<
    InputHTMLAttributes,
    'type' | 'innerHTML' | 'textContent' | 'autocomplete'
> {
    // Avoid expanding Vue's `autocomplete` token union in `withDefaults()`.
    autocomplete?: string
    defaultChecked?: boolean
}

interface CheckboxProps extends /* @vue-ignore */ CheckboxInputAttributes {
    valid?: boolean
    modelValue?: boolean
}

defineOptions({ inheritAttrs: false })
withDefaults(defineProps<CheckboxProps>(), {
    valid: true,
    modelValue: undefined,
})
const emit = defineEmits<{
    'update:modelValue': [value: boolean]
}>()
defineSlots<{}>()
</script>

<template>
    <input
        v-bind="{
            ...$attrs,
            ...(modelValue === undefined ? {} : { checked: modelValue }),
        }"
        type="checkbox"
        :class="['fi-checkbox-input', valid ? 'fi-valid' : 'fi-invalid']"
        @change="
            emit(
                'update:modelValue',
                ($event.target as HTMLInputElement).checked,
            )
        "
    />
</template>
