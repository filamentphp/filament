<script>
    import Fieldset from '../../../packages/support/resources/js/svelte/Fieldset.svelte'
    let { rich, ...props } = $props()
</script>

{#snippet richLabel()}<strong>Address</strong>{/snippet}
<Fieldset {...props} label={rich ? richLabel : props.label}>
    <input aria-label="Street" name="street" />
</Fieldset>
