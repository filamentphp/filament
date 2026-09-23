<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import Icon from './Icon.vue'
import type { IconSize } from '../components/icon'

interface CalloutProps extends /* @vue-ignore */ Omit<
    HTMLAttributes,
    'innerHTML' | 'textContent'
> {
    color?: string
    heading?: string | null
    description?: string | null
    footer?: string | null
    controls?: string | null
    iconColor?: string
    iconSize?: IconSize
}
withDefaults(defineProps<CalloutProps>(), { color: 'gray', iconSize: 'lg' })
defineSlots<{
    default?(): unknown
    heading?(): unknown
    description?(): unknown
    footer?(): unknown
    controls?(): unknown
    icon?(): unknown
}>()
</script>

<template>
    <div
        :class="[
            'fi-callout',
            color !== 'gray' && `fi-color fi-color-${color}`,
        ]"
    >
        <Icon
            v-if="$slots.icon"
            :size="iconSize"
            aria-hidden="true"
            :class="[
                'fi-callout-icon',
                (iconColor ?? color) !== 'gray' &&
                    `fi-color fi-color-${iconColor ?? color}`,
            ]"
            ><slot name="icon"
        /></Icon>
        <div
            v-if="
                $slots.heading ||
                heading?.trim() ||
                $slots.description ||
                description?.trim() ||
                $slots.footer ||
                $slots.default ||
                footer?.trim()
            "
            class="fi-callout-main"
        >
            <div
                v-if="
                    $slots.heading ||
                    heading?.trim() ||
                    $slots.description ||
                    description?.trim()
                "
                class="fi-callout-text"
            >
                <h4
                    v-if="$slots.heading || heading?.trim()"
                    class="fi-callout-heading"
                >
                    <slot name="heading">{{ heading }}</slot>
                </h4>
                <p
                    v-if="$slots.description || description?.trim()"
                    class="fi-callout-description"
                >
                    <slot name="description">{{ description }}</slot>
                </p>
            </div>
            <div
                v-if="$slots.footer || $slots.default || footer?.trim()"
                class="fi-callout-footer"
            >
                <slot name="footer"
                    ><slot>{{ footer }}</slot></slot
                >
            </div>
        </div>
        <div
            v-if="$slots.controls || controls?.trim()"
            class="fi-callout-controls"
        >
            <slot name="controls">{{ controls }}</slot>
        </div>
    </div>
</template>
