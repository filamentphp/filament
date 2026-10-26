<script setup lang="ts">
import type { ImgHTMLAttributes } from 'vue'
import { getAvatarClasses } from '../components/avatar'

// Vue 3.3 does not include `loading` in its native image attribute types.
interface AvatarImageAttributes extends ImgHTMLAttributes {
    loading?: 'eager' | 'lazy'
}

interface AvatarProps extends /* @vue-ignore */ AvatarImageAttributes {
    alt?: string
    circular?: boolean
    size?: string
}

withDefaults(defineProps<AvatarProps>(), {
    alt: '',
    circular: true,
    size: 'md',
})
</script>

<template>
    <img :alt="alt" :class="getAvatarClasses(circular, size)" />
</template>
