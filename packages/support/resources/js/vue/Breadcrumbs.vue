<script setup lang="ts">
import { computed, type AnchorHTMLAttributes, type HTMLAttributes } from 'vue'
import {
    breadcrumbSeparator,
    breadcrumbSeparatorRtl,
} from '../components/breadcrumbs'

type BreadcrumbItem = Omit<
    AnchorHTMLAttributes,
    'innerHTML' | 'textContent'
> & { label: string }

interface BreadcrumbsProps extends /* @vue-ignore */ HTMLAttributes {
    breadcrumbs?: readonly BreadcrumbItem[]
}

const props = withDefaults(defineProps<BreadcrumbsProps>(), {
    breadcrumbs: () => [],
})
const items = computed(() =>
    props.breadcrumbs.map(({ label, class: className, ...attributes }) => ({
        label,
        className,
        attributes,
    })),
)
</script>

<template>
    <nav aria-label="Breadcrumbs" class="fi-breadcrumbs">
        <ol class="fi-breadcrumbs-list">
            <li
                v-for="(item, index) in items"
                :key="index"
                class="fi-breadcrumbs-item"
            >
                <template v-if="index > 0">
                    <span
                        v-if="$slots.separator"
                        aria-hidden="true"
                        class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-ltr"
                        ><slot name="separator"
                    /></span>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        v-bind="{ 'data-slot': 'icon' }"
                        class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-ltr"
                    >
                        <path
                            fill-rule="evenodd"
                            :d="breadcrumbSeparator"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span
                        v-if="$slots.separatorRtl"
                        aria-hidden="true"
                        class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-rtl"
                        ><slot name="separatorRtl"
                    /></span>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        v-bind="{ 'data-slot': 'icon' }"
                        class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-rtl"
                    >
                        <path
                            fill-rule="evenodd"
                            :d="breadcrumbSeparatorRtl"
                            clip-rule="evenodd"
                        />
                    </svg>
                </template>
                <component
                    :is="item.attributes.href === undefined ? 'span' : 'a'"
                    v-bind="item.attributes"
                    :aria-current="
                        index === items.length - 1 ? 'page' : undefined
                    "
                    :class="['fi-breadcrumbs-item-label', item.className]"
                    >{{ item.label }}</component
                >
            </li>
        </ol>
    </nav>
</template>
