<script setup lang="ts">
import Icon from '../../../packages/support/resources/js/vue/Icon.vue'
type Props = InstanceType<typeof Icon>['$props']
// @ts-expect-error Sizes match `IconSize`.
const invalidSize: Props = { size: 'huge' }
// @ts-expect-error Raw markup is unsupported.
const invalidHtml: Props = { innerHTML: '' }
void invalidSize
void invalidHtml
</script>
<template>
    <Icon
        size="sm"
        role="img"
        aria-label="Saved"
        @click="(event) => event.preventDefault()"
        ><svg viewBox="0 0 24 24"><path d="M4 12h16" /></svg
    ></Icon>
    <Icon
        src="/icon.svg"
        srcset="/icon.svg 1x"
        decoding="async"
        alt="Saved"
        loading="lazy"
    />
</template>
