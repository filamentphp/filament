<script>
    import Badge from '../../../packages/support/resources/js/svelte/Badge.svelte'
    let {
        onElement,
        label = 'Priority',
        controlledSequence = false,
        ...props
    } = $props()
    let value = $state('')
    // Recompute an equal array when the controlled input changes.
    const sequenceKeys = (value) => ['g p']
    let element = $state()
    $effect(() => {
        onElement(element)
        return () => onElement(undefined)
    })
</script>

{#if controlledSequence}<input
        aria-label="Sequence input"
        data-testid="sequence-input"
        bind:value
    />{/if}
<Badge
    {...props}
    keyBindings={controlledSequence ? sequenceKeys(value) : props.keyBindings}
    bind:element>{label}</Badge
>
{#if controlledSequence}<output data-testid="sequence-value">{value}</output
    >{/if}
