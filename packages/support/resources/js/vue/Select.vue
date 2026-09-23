<script setup lang="ts" generic="Value = unknown">
import { computed, getCurrentInstance, ref } from 'vue'
import type { SelectHTMLAttributes } from 'vue'

interface SelectProps extends /* @vue-ignore */ Omit<
    SelectHTMLAttributes,
    'value' | 'innerHTML' | 'textContent'
> {
    inlinePrefix?: boolean
    modelValue?: Value
    modelModifiers?: { number?: boolean; trim?: boolean; lazy?: boolean }
}

defineOptions({ inheritAttrs: false })
const props = defineProps<SelectProps>()
const emit = defineEmits<{
    'update:modelValue': [value: Value]
}>()
defineSlots<{ default(): unknown }>()
const element = ref<HTMLSelectElement>()
defineExpose({ element })
// Keep native `v-model` and uncontrolled option selection distinct for the component's lifetime.
const initialProps = getCurrentInstance()!.vnode.props ?? {}
const hasModel = 'modelValue' in initialProps || 'model-value' in initialProps
const selection = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value as Value),
})
</script>

<template>
    <select
        v-if="hasModel"
        ref="element"
        v-model="selection"
        v-bind="$attrs"
        :class="[
            'fi-select-input',
            inlinePrefix && 'fi-select-input-has-inline-prefix',
        ]"
    >
        <slot />
    </select>
    <select
        v-else
        ref="element"
        v-bind="$attrs"
        :class="[
            'fi-select-input',
            inlinePrefix && 'fi-select-input-has-inline-prefix',
        ]"
    >
        <slot />
    </select>
</template>
