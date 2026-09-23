<script>
    import Input from '../../../packages/support/resources/js/svelte/Input.svelte'
    import InputWrapper from '../../../packages/support/resources/js/svelte/InputWrapper.svelte'
    let { configuration, report, reportInput } = $props()
    let amount = $state(0)
    let element = $state()
    $effect(() => report(element, amount))
</script>

<form
    onsubmit={(event) => {
        event.preventDefault()
        report(element, amount, new FormData(event.currentTarget))
    }}
>
    <InputWrapper
        disabled={configuration.disabled}
        inlinePrefix={configuration.inline}
        prefix="£"
    >
        <Input
            bind:element
            bind:value={amount}
            type="number"
            name="amount"
            aria-label="Amount"
            min="0"
            required
            defaultValue={0}
            disabled={configuration.disabled}
            readonly={configuration.readOnly}
            inlinePrefix={configuration.inline}
            oninput={reportInput}
        />
    </InputWrapper>
    <InputWrapper
        ><Input
            name="title"
            aria-label="Title"
            defaultValue="Workshop"
        /></InputWrapper
    >
    <InputWrapper
        ><Input
            type="email"
            name="email"
            aria-label="Email"
            required
            defaultValue="ada@example.com"
        /></InputWrapper
    >
    <button type="submit" data-testid="submit">Submit</button><button
        type="reset"
        data-testid="reset">Reset</button
    >
    <button type="button" data-testid="zero" onclick={() => (amount = 0)}
        >Zero</button
    >
    <button
        type="button"
        data-testid="empty"
        onclick={() => (amount = undefined)}>Empty</button
    >
</form>
<form data-testid="uncontrolled">
    <InputWrapper
        ><Input
            type="number"
            name="zero"
            aria-label="Uncontrolled number"
            defaultValue={0}
        /></InputWrapper
    >
    <InputWrapper
        ><Input
            name="empty"
            aria-label="Uncontrolled text"
            defaultValue=""
        /></InputWrapper
    >
    <button type="reset">Reset uncontrolled</button>
</form>
