<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { getActionsClasses } from '../components/actions'

interface ActionsProps extends /* @vue-ignore */ Omit<
    HTMLAttributes,
    'innerHTML' | 'textContent'
> {
    alignment?: string | null
    fullWidth?: boolean
}
defineProps<ActionsProps>()
defineSlots<{ default?(): unknown }>()
</script>

<template>
    <div :class="getActionsClasses(alignment, fullWidth)"><slot /></div>
</template>
