<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Avatar from '../../../packages/support/resources/js/svelte/Avatar.svelte'

    // @ts-expect-error `loading` only accepts native image loading values.
    const invalidLoading: ComponentProps<typeof Avatar> = { loading: 'invalid' }
    void invalidLoading
</script>

<Avatar
    src="/portrait.jpg"
    alt="Profile"
    loading="lazy"
    class="profile-avatar"
    circular={false}
    size="lg"
    onload={(event) => event.preventDefault()}
/>
