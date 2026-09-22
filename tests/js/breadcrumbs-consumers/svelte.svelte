<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Breadcrumbs from '../../../packages/support/resources/js/svelte/Breadcrumbs.svelte'
    const missingLabel: ComponentProps<typeof Breadcrumbs> = {
        // @ts-expect-error `label` is required.
        breadcrumbs: [{ href: '/' }],
    }
    const invalidHref: ComponentProps<typeof Breadcrumbs> = {
        // @ts-expect-error `href` must be a string.
        breadcrumbs: [{ label: 'Home', href: 42 }],
    }
    void missingLabel
    void invalidHref
</script>

<Breadcrumbs
    aria-label="Location"
    breadcrumbs={[
        {
            label: 'Home',
            href: '/',
            target: '_blank',
            onclick: (event) => event.currentTarget.focus(),
        },
        { label: 'Current' },
    ]}
>
    {#snippet separator()}/{/snippet}
    {#snippet separatorRtl()}\{/snippet}
</Breadcrumbs>
