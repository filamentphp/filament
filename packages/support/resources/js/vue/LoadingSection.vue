<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { getLoadingSectionLayout } from '../components/loading-section'

interface LoadingSectionProps extends /* @vue-ignore */ Omit<
    HTMLAttributes,
    'innerHTML' | 'textContent'
> {
    columnSpan?: number | string | null | Record<string, number | string | null>
    columnStart?:
        | number
        | string
        | null
        | Record<string, number | string | null>
    height?: string | null
    loadingLabel?: string | null
}
defineProps<LoadingSectionProps>()
</script>

<template>
    <div
        role="status"
        aria-busy="true"
        :class="
            getLoadingSectionLayout({ columnSpan, columnStart, height })
                .className
        "
        :style="
            getLoadingSectionLayout({ columnSpan, columnStart, height }).style
        "
    >
        <span class="fi-sr-only">{{ loadingLabel ?? 'Loading...' }}</span>
    </div>
</template>
