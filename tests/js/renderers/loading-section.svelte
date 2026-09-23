<script>
    import LoadingSection from '../../../packages/support/resources/js/svelte/LoadingSection.svelte'
    let { state: loadingState, onElement, ...props } = $props()
    let element = $state()
    $effect(() => {
        onElement(element)
        return () => onElement(undefined)
    })
</script>

{#if loadingState === 'loading'}<LoadingSection
        {...props}
        bind:element
    />{:else}<p role="status">
        {loadingState}
    </p>{/if}
