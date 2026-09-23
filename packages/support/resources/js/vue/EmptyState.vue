<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import Icon from './Icon.vue'
import type { IconSize } from '../components/icon'

interface EmptyStateProps extends /* @vue-ignore */ Omit<
    HTMLAttributes,
    'innerHTML' | 'textContent'
> {
    compact?: boolean
    contained?: boolean
    heading?: string | null
    headingTag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
    description?: string | null
    footer?: string | null
    iconColor?: string
    iconSize?: IconSize
}
withDefaults(defineProps<EmptyStateProps>(), {
    compact: false,
    contained: true,
    headingTag: 'h2',
    iconColor: 'primary',
    iconSize: 'lg',
})
defineSlots<{
    default?(): unknown
    heading?(): unknown
    description?(): unknown
    footer?(): unknown
    icon?(): unknown
}>()
</script>

<template>
    <div
        :class="[
            'fi-empty-state',
            {
                'fi-compact': compact,
                'fi-empty-state-not-contained': !contained,
            },
        ]"
    >
        <div class="fi-empty-state-content">
            <div
                v-if="$slots.icon"
                :class="[
                    'fi-empty-state-icon-bg',
                    iconColor !== 'gray' && `fi-color fi-color-${iconColor}`,
                ]"
            >
                <Icon
                    :size="iconSize"
                    :class="
                        iconColor !== 'gray'
                            ? `fi-color fi-color-${iconColor}`
                            : undefined
                    "
                    ><slot name="icon"
                /></Icon>
            </div>
            <div class="fi-empty-state-text-ctn">
                <component :is="headingTag" class="fi-empty-state-heading"
                    ><slot name="heading">{{ heading }}</slot></component
                >
                <p
                    v-if="$slots.description || description?.trim()"
                    class="fi-empty-state-description"
                >
                    <slot name="description">{{ description }}</slot>
                </p>
                <footer
                    v-if="$slots.footer || $slots.default || footer?.trim()"
                    class="fi-empty-state-footer"
                >
                    <slot name="footer"
                        ><slot>{{ footer }}</slot></slot
                    >
                </footer>
            </div>
        </div>
    </div>
</template>
