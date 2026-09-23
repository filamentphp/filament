<script setup lang="ts">
import type { HTMLAttributes } from 'vue'

interface InputWrapperProps extends /* @vue-ignore */ Omit<
    HTMLAttributes,
    'prefix'
> {
    disabled?: boolean
    valid?: boolean
    inlinePrefix?: boolean
    inlineSuffix?: boolean
    prefix?: string | null
    suffix?: string | null
}

withDefaults(defineProps<InputWrapperProps>(), {
    disabled: false,
    valid: true,
    inlinePrefix: false,
    inlineSuffix: false,
    prefix: null,
    suffix: null,
})

defineSlots<{
    default?(): unknown
    prefix?(): unknown
    suffix?(): unknown
    prefixIcon?(): unknown
    suffixIcon?(): unknown
    prefixActions?(): unknown
    suffixActions?(): unknown
}>()
</script>

<template>
    <div
        :class="[
            'fi-input-wrp',
            { 'fi-disabled': disabled, 'fi-invalid': !valid },
        ]"
    >
        <div
            v-if="
                $slots.prefix ||
                prefix?.trim() ||
                $slots.prefixIcon ||
                $slots.prefixActions
            "
            :class="[
                'fi-input-wrp-prefix',
                'fi-input-wrp-prefix-has-content',
                {
                    'fi-inline': inlinePrefix,
                    'fi-input-wrp-prefix-has-label':
                        $slots.prefix || !!prefix?.trim(),
                },
            ]"
        >
            <div v-if="$slots.prefixActions" class="fi-input-wrp-actions">
                <slot name="prefixActions" />
            </div>
            <slot name="prefixIcon" />
            <span
                v-if="$slots.prefix || prefix?.trim()"
                class="fi-input-wrp-label"
                ><slot name="prefix">{{ prefix }}</slot></span
            >
        </div>
        <div class="fi-input-wrp-content-ctn"><slot /></div>
        <div
            v-if="
                $slots.suffix ||
                suffix?.trim() ||
                $slots.suffixIcon ||
                $slots.suffixActions
            "
            :class="[
                'fi-input-wrp-suffix',
                {
                    'fi-inline': inlineSuffix,
                    'fi-input-wrp-suffix-has-label':
                        $slots.suffix || !!suffix?.trim(),
                },
            ]"
        >
            <span
                v-if="$slots.suffix || suffix?.trim()"
                class="fi-input-wrp-label"
                ><slot name="suffix">{{ suffix }}</slot></span
            >
            <slot name="suffixIcon" />
            <div v-if="$slots.suffixActions" class="fi-input-wrp-actions">
                <slot name="suffixActions" />
            </div>
        </div>
    </div>
</template>
