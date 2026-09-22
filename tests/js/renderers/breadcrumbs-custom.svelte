<script>
    import Breadcrumbs from '../../../packages/support/resources/js/svelte/Breadcrumbs.svelte'
    let props = $props()
</script>

<Breadcrumbs {...props}>
    {#snippet separator()}/{/snippet}
    {#snippet separatorRtl()}\{/snippet}
</Breadcrumbs>
