<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import LoadingIndicator from '../../../packages/support/resources/js/svelte/LoadingIndicator.svelte'
    const invalidSize: ComponentProps<typeof LoadingIndicator> = {
        // @ts-expect-error Sizes match `IconSize`.
        size: 'huge',
    }
    const invalidChildren: ComponentProps<typeof LoadingIndicator> = {
        // @ts-expect-error The component owns its paths.
        children: () => '',
    }
    void invalidSize
    void invalidChildren
</script>

<LoadingIndicator
    size="lg"
    aria-hidden={false}
    role="img"
    aria-label="Loading"
    viewBox="0 0 32 32"
    onclick={(event) => event.currentTarget.getBBox()}
/>
