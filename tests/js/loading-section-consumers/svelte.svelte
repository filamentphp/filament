<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import LoadingSection from '../../../packages/support/resources/js/svelte/LoadingSection.svelte'
    let element = $state<HTMLDivElement>()
    // @ts-expect-error `height` is a CSS length, not a pixel count.
    const invalid: ComponentProps<typeof LoadingSection> = { height: 12 }
    void invalid
</script>

<LoadingSection
    bind:element
    columnSpan={{ default: 'full', lg: 2 }}
    columnStart={3}
    height="12rem"
    loadingLabel="Loading projects"
    title="Projects"
    onclick={(event) => event.currentTarget.focus()}
/>
