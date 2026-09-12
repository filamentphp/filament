<script>
    let {
        value,
        config,
        id,
        ariaDescribedBy,
        disabled,
        readOnly,
        required,
        invalid,
        onChange,
        onBlur,
        utilities,
    } = $props()
    let report = $state('')
    const locked = $derived(disabled || readOnly)
    function inspect() {
        report = JSON.stringify({
            path: utilities.$statePath,
            state: utilities.$state,
            sibling: utilities.$get('caption'),
            root: utilities.$get('data.caption', true),
            relative: utilities.$get('../../caption'),
        })
        utilities.$set('caption', 'Changed by renderer')
        utilities.$set('data.caption', 'Changed root by renderer', true, true)
    }
</script>

<div>
    <input
        {id}
        aria-label="Title"
        aria-describedby={ariaDescribedBy}
        value={value.title}
        {disabled}
        readonly={readOnly}
        {required}
        aria-invalid={invalid}
        oninput={(event) => onChange({ ...value, title: event.target.value })}
        onblur={onBlur}
    />
    <label
        ><input
            type="checkbox"
            checked={value.enabled}
            disabled={locked}
            onchange={(event) =>
                onChange({ ...value, enabled: event.target.checked })}
            onblur={onBlur}
        />Enabled</label
    >
    <button
        type="button"
        disabled={locked}
        onclick={() =>
            onChange({
                ...value,
                tags: value.tags.includes('sms') ? [] : ['sms', 'push'],
            })}
        onblur={onBlur}>Toggle channels</button
    >
    <output data-channels>{JSON.stringify(value.tags)}</output>
    <output data-config>{JSON.stringify(config)}</output>
    <button type="button" disabled={locked} onclick={inspect}
        >Use utilities</button
    >
    <output data-report>{report}</output>
    <button
        type="button"
        onclick={async () => {
            const { $replaceTitle: replaceTitle } = utilities
            report = JSON.stringify(
                await replaceTitle({
                    title: 'PHP café replacement',
                }),
            )
        }}>Call field method</button
    >
    <button
        type="button"
        onclick={async () => {
            report = JSON.stringify(
                await utilities.$callSchemaComponentMethod('inspectTitle', {
                    prefix: 'Read: ',
                }),
            )
        }}>Call renderless method</button
    >
    <button
        type="button"
        onclick={async () => {
            report = JSON.stringify(
                await utilities.$callSchemaComponentMethod('unexposedMethod'),
            )
        }}>Call unexposed method</button
    >
    <button
        type="button"
        onclick={async () => {
            report = JSON.stringify(
                await utilities.$wire.$call('changeCaption', 'Via $wire café'),
            )
        }}>Call Livewire method</button
    >
</div>
