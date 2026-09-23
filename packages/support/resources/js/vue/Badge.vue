<script setup lang="ts">
import {
    ref,
    watchEffect,
    type AnchorHTMLAttributes,
    type ButtonHTMLAttributes,
} from 'vue'
import { getBadgeClasses } from '../components/badge'
import type { IconSize } from '../components/icon'
import { interactive } from '../components/interactive'
import Icon from './Icon.vue'
import LoadingIndicator from './LoadingIndicator.vue'
defineOptions({ inheritAttrs: false })
interface BadgeProps extends /* @vue-ignore */ Omit<
    AnchorHTMLAttributes & ButtonHTMLAttributes,
    'color' | 'disabled' | 'innerHTML' | 'textContent'
> {
    tag?: 'span' | 'a' | 'button'
    color?: string
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'
    iconPosition?: 'before' | 'after'
    iconSize?: IconSize
    loading?: boolean
    disabled?: boolean
    tooltip?: string | null
    keyBindings?: string[]
    deleteLabel?: string
    deleteLoading?: boolean
    onDelete?: (event: MouseEvent) => void
}
const props = withDefaults(defineProps<BadgeProps>(), {
    tag: 'span',
    color: 'primary',
    size: 'md',
    iconPosition: 'before',
    iconSize: 'sm',
    deleteLabel: 'Delete',
})
const element = ref<HTMLElement>()
defineExpose({ element })
defineSlots<{ default?(): unknown; icon?(): unknown }>()
watchEffect((cleanup) => {
    if (props.onDelete && props.tag !== 'span')
        throw new Error('Deletable badges must use tag="span".')
    if (element.value)
        cleanup(
            interactive(element.value, {
                tooltip: props.tooltip,
                keyBindings: props.keyBindings,
                disabled: props.disabled || props.loading,
            }),
        )
})
function click(event: MouseEvent) {
    if (props.disabled || props.loading) {
        event.preventDefault()
        event.stopImmediatePropagation()
    }
}
</script>
<template>
    <component
        :is="tag"
        ref="element"
        v-bind="$attrs"
        :class="getBadgeClasses(props)"
        :href="tag === 'a' && !(disabled || loading) ? $attrs.href : undefined"
        :role="
            $attrs.role ??
            (tag === 'a' && $attrs.href != null && (disabled || loading)
                ? 'link'
                : undefined)
        "
        :type="tag === 'button' ? ($attrs.type ?? 'button') : undefined"
        :disabled="
            tag === 'button' && (disabled || loading) && !tooltip
                ? true
                : undefined
        "
        :aria-disabled="disabled || loading || undefined"
        :aria-busy="loading || undefined"
        :tabindex="
            (disabled || loading) && tooltip
                ? ($attrs.tabindex ?? 0)
                : $attrs.tabindex
        "
        :style="
            (disabled || loading) && tooltip
                ? { pointerEvents: 'auto' }
                : undefined
        "
        @click.capture="click"
    >
        <template v-if="iconPosition === 'before'">
            <LoadingIndicator v-if="loading" :size="iconSize" />
            <Icon v-else-if="$slots.icon" :size="iconSize" aria-hidden="true"
                ><slot name="icon"
            /></Icon>
        </template>
        <span class="fi-badge-label-ctn"
            ><span class="fi-badge-label"><slot /></span
        ></span>
        <button
            v-if="onDelete"
            type="button"
            class="fi-badge-delete-btn"
            :disabled="disabled || loading || deleteLoading"
            :aria-busy="deleteLoading || undefined"
            @click.stop="
                (event) => {
                    if (!(disabled || loading || deleteLoading))
                        onDelete?.(event)
                }
            "
        >
            <LoadingIndicator v-if="deleteLoading" size="xs" />
            <span v-else class="fi-badge-delete-btn-icon" aria-hidden="true" />
            <span class="fi-sr-only">{{
                deleteLabel.trim() ? deleteLabel : 'Delete'
            }}</span>
        </button>
        <template v-else-if="iconPosition === 'after'">
            <LoadingIndicator v-if="loading" :size="iconSize" />
            <Icon v-else-if="$slots.icon" :size="iconSize" aria-hidden="true"
                ><slot name="icon"
            /></Icon>
        </template>
    </component>
</template>
