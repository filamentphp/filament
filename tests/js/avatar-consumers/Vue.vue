<script setup lang="ts">
import Avatar from '../../../packages/support/resources/js/vue/Avatar.vue'

type AvatarProps = InstanceType<typeof Avatar>['$props']

// @ts-expect-error `loading` only accepts native image loading values.
const invalidLoading: AvatarProps = { loading: 'invalid' }
void invalidLoading
</script>

<template>
    <Avatar
        src="/portrait.jpg"
        alt="Profile"
        loading="lazy"
        class="profile-avatar"
        :circular="false"
        size="lg"
        @load="(event) => event.preventDefault()"
    />
</template>
