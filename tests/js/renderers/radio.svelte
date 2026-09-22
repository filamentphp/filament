<script>
    import Radio from '../../../packages/support/resources/js/svelte/Radio.svelte'
    let { settings, cases, form, reportChange } = $props()
</script>

{#each cases as attributes, index (index)}
    <label>
        <Radio
            {...attributes}
            {form}
            valid={settings.valid}
            onchange={reportChange}
        />
        {attributes.name}
        {attributes.value}
    </label>
{/each}
{#each ['standard', 'express'] as value (value)}
    <label>
        <Radio
            bind:group={settings.delivery}
            {value}
            name="controlled"
            {form}
            valid={settings.valid}
            onchange={(event) => {
                event.currentTarget.dataset.modelAtChange = settings.delivery
                reportChange(event)
            }}
        />
        {value}
    </label>
{/each}
<output data-model={settings.delivery}>{settings.delivery}</output>
