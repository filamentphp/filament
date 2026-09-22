<script setup lang="ts">
import type { SVGAttributes } from 'vue'
import {
    getLoadingIndicatorClasses,
    loadingIndicatorTrack,
    loadingIndicatorArc,
    type LoadingIndicatorSize,
} from '../components/loading-indicator'

interface LoadingIndicatorProps extends /* @vue-ignore */ Omit<
    SVGAttributes,
    'innerHTML' | 'textContent'
> {
    size?: LoadingIndicatorSize
}

withDefaults(defineProps<LoadingIndicatorProps>(), { size: 'md' })
defineSlots<{}>()
</script>

<template>
    <svg
        fill="none"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
        :class="getLoadingIndicatorClasses(size)"
    >
        <path
            clip-rule="evenodd"
            :d="loadingIndicatorTrack"
            fill-rule="evenodd"
            fill="currentColor"
            opacity="0.2"
        ></path>
        <path :d="loadingIndicatorArc" fill="currentColor"></path>
    </svg>
</template>
