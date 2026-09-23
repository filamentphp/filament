<script>
    import Badge from '../../../packages/support/resources/js/svelte/Badge.svelte'
    let { onElement, label = 'Priority', ...props } = $props()
    let element = $state()
    $effect(() => {
        onElement(element)
        return () => onElement(undefined)
    })
</script>

<Badge {...props} bind:element>{label}</Badge>
