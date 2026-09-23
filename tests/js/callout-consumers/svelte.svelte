<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Callout from '../../../packages/support/resources/js/svelte/Callout.svelte'
    let element = $state<HTMLDivElement>()
    // @ts-expect-error `iconSize` must be a supported size.
    const invalid: ComponentProps<typeof Callout> = { iconSize: 'huge' }
    void invalid
</script>

<Callout
    bind:element
    color="brand"
    description="Review"
    iconColor="gray"
    iconSize="sm"
    title="Notice"
    onclick={(event) => event.currentTarget.focus()}
>
    {#snippet heading()}<strong>Notice</strong>{/snippet}
    {#snippet icon()}<svg />{/snippet}
    {#snippet controls()}<button>Help</button>{/snippet}
    <button>Continue</button>
</Callout>
