<script setup lang="ts">
import { ref } from 'vue'
import Badge from '../../../packages/support/resources/js/vue/Badge.vue'
const badge = ref<InstanceType<typeof Badge>>()
function focus() {
    badge.value?.element?.focus()
}
</script>
<template>
    <Badge
        ref="badge"
        tag="button"
        type="submit"
        form="filters"
        tooltip="Save filters"
        :key-bindings="['mod+shift+b']"
        @click="focus"
        >Save</Badge
    >
    <Badge
        color="brand"
        size="xs"
        icon-position="after"
        icon-size="lg"
        :on-delete="(event: MouseEvent) => event.preventDefault()"
        delete-label="Remove priority"
        delete-loading
        >Priority<template #icon><svg /></template
    ></Badge>
</template>
