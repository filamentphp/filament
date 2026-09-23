<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import EmptyState from '../../../packages/support/resources/js/svelte/EmptyState.svelte'
    let element = $state<HTMLDivElement>()
    // @ts-expect-error `headingTag` must be a heading element.
    const invalid: ComponentProps<typeof EmptyState> = { headingTag: 'script' }
    void invalid
</script>

<EmptyState
    bind:element
    headingTag="h3"
    compact
    contained={false}
    iconSize="lg"
    iconColor="success"
    title="Projects"
    onclick={(event) => event.currentTarget.focus()}
>
    {#snippet heading()}<strong>Projects</strong>{/snippet}
    {#snippet description()}<em>Start here</em>{/snippet}
    {#snippet icon()}<svg />{/snippet}
    <button>Create</button>
</EmptyState>
