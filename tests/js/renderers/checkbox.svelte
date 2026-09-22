<script>
    import Checkbox from '../../../packages/support/resources/js/svelte/Checkbox.svelte'
    let { settings, cases, form, reportChange } = $props()
</script>

{#each cases as attributes (attributes.name)}
    <label>
        <Checkbox
            {...attributes}
            {form}
            valid={settings.valid}
            onchange={reportChange}
        />
        {attributes.name}
    </label>
{/each}
<label>
    <Checkbox
        bind:checked={settings.checked}
        valid={settings.valid}
        {form}
        name="controlled"
        value="enabled"
        onchange={reportChange}
    />
    Controlled
</label>
<output data-model={String(settings.checked)}>{String(settings.checked)}</output
>
