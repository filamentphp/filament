<script>
    import Select from '../../../packages/support/resources/js/svelte/Select.svelte'
    import InputWrapper from '../../../packages/support/resources/js/svelte/InputWrapper.svelte'
    let {
        configuration,
        report,
        reportElement,
        reportChange,
        reportSubmission,
        reportOptionDefault,
    } = $props()
    let value = $state('drawing')
    let multiple = $state(['ceramics'])
    let defaultMultiple = $state()
    let optionDefault = $state('drawing')
    let element = $state()
    $effect(() => reportElement(element))
    $effect(() => report(value, multiple))
    $effect(() => reportOptionDefault(optionDefault))
</script>

{#snippet options()}
    <option value="">Choose a workshop</option>
    {#if !configuration.remove}<option value="drawing">drawing</option>{/if}
    <option value="ceramics">ceramics</option>
    <option value="archived" disabled>archived</option>
{/snippet}
<form
    onsubmit={(event) => {
        event.preventDefault()
        reportSubmission(new FormData(event.currentTarget))
    }}
>
    <InputWrapper
        prefix="Art"
        disabled={configuration.disabled}
        inlinePrefix={configuration.inline}
    >
        <Select
            name="workshop"
            aria-label="workshop"
            required
            bind:element
            bind:value
            defaultValue="drawing"
            disabled={configuration.disabled}
            inlinePrefix={configuration.inline}
            onchange={() => reportChange(value)}>{@render options()}</Select
        >
    </InputWrapper>
    <InputWrapper
        prefix="Art"
        disabled={configuration.disabled}
        inlinePrefix={configuration.inline}
    >
        <Select
            name="extras"
            aria-label="extras"
            multiple
            bind:value={multiple}
            defaultValue={['ceramics']}
            disabled={configuration.disabled}
            inlinePrefix={configuration.inline}>{@render options()}</Select
        >
    </InputWrapper>
    <button type="submit" data-testid="submit">Submit</button>
    <button type="reset" data-testid="reset">Reset</button>
    <button
        type="button"
        data-testid="empty"
        onclick={() => {
            value = ''
            multiple = []
        }}>Empty</button
    >
    <button
        type="button"
        data-testid="archived"
        onclick={() => {
            value = 'archived'
            multiple = ['drawing', 'archived']
        }}>Archived</button
    >
</form>
<form data-testid="defaults">
    <InputWrapper
        prefix="Art"
        disabled={configuration.disabled}
        inlinePrefix={configuration.inline}
    >
        <Select
            name="defaultWorkshop"
            aria-label="defaultWorkshop"
            required
            defaultValue="ceramics"
            disabled={configuration.disabled}
            inlinePrefix={configuration.inline}>{@render options()}</Select
        >
    </InputWrapper>
    <InputWrapper>
        <Select
            name="defaultExtras"
            aria-label="defaultExtras"
            multiple
            bind:value={defaultMultiple}
            defaultValue={['ceramics', 'archived']}>{@render options()}</Select
        >
    </InputWrapper>
    <button type="reset">Reset defaults</button>
</form>
<form data-testid="option-defaults">
    <InputWrapper>
        <Select
            name="optionDefault"
            aria-label="Option default"
            disabled={configuration.disabled}
        >
            <option value="drawing">Drawing</option>
            <option value="ceramics" selected>Ceramics</option>
            <option value="printing">Printing</option>
        </Select>
    </InputWrapper>
    <InputWrapper>
        <Select
            name="boundOptionDefault"
            aria-label="Bound option default"
            bind:value={optionDefault}
            disabled={configuration.disabled}
        >
            <option value="drawing">Drawing</option>
            <option value="ceramics" selected>Ceramics</option>
            <option value="printing">Printing</option>
        </Select>
    </InputWrapper>
    <InputWrapper>
        <Select
            name="nullDefault"
            aria-label="Null default"
            defaultValue={null}
        >
            <option value="drawing">Drawing</option>
            <option value="ceramics" selected>Ceramics</option>
        </Select>
    </InputWrapper>
    <button type="reset">Reset option defaults</button>
</form>
