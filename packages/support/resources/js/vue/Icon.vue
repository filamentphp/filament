<script setup lang="ts">
import type { ImgHTMLAttributes } from 'vue'
import { getIconClasses, type IconSize } from '../components/icon'

interface IconProps extends /* @vue-ignore */ Omit<
    ImgHTMLAttributes,
    'innerHTML' | 'textContent'
> {
    src?: string
    alt?: string
    size?: IconSize
    loading?: 'eager' | 'lazy'
}

withDefaults(defineProps<IconProps>(), { alt: '', size: 'md' })
</script>

<template>
    <img v-if="src" :src="src" :alt="alt" :class="getIconClasses(size)" />
    <span v-else-if="$slots.default" :class="getIconClasses(size)"
        ><slot
    /></span>
</template>
