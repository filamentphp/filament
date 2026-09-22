<script setup lang="ts">
import type { FieldsetHTMLAttributes } from 'vue'

interface FieldsetProps extends /* @vue-ignore */ FieldsetHTMLAttributes {
    contained?: boolean
    label?: string | null
    labelHidden?: boolean
    required?: boolean
}

withDefaults(defineProps<FieldsetProps>(), {
    contained: true,
    label: null,
    labelHidden: false,
    required: false,
})

defineSlots<{
    default?(): unknown
    label?(): unknown
}>()
</script>

<template>
    <fieldset
        :class="[
            'fi-fieldset',
            {
                'fi-fieldset-label-hidden': labelHidden,
                'fi-fieldset-not-contained': !contained,
            },
        ]"
    >
        <legend v-if="$slots.label || label?.trim()">
            <slot name="label">{{ label }}</slot
            ><sup v-if="required" class="fi-fieldset-label-required-mark"
                >*</sup
            >
        </legend>
        <slot />
    </fieldset>
</template>
