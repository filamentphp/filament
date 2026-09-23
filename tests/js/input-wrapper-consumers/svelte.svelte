<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import InputWrapper from '../../../packages/support/resources/js/svelte/InputWrapper.svelte'
    import Icon from '../../../packages/support/resources/js/svelte/Icon.svelte'
    let element = $state<HTMLDivElement>()
    const invalid: ComponentProps<typeof InputWrapper> = {
        // @ts-expect-error Wrapper state is boolean.
        valid: 'false',
    }
    void invalid
</script>

<InputWrapper
    bind:element
    disabled
    valid={false}
    inlinePrefix
    prefix="£"
    onclick={(event) => event.currentTarget.focus()}
>
    {#snippet prefixIcon()}<Icon src="/currency.svg" />{/snippet}
    {#snippet suffix()}<strong>GBP</strong>{/snippet}
    {#snippet suffixActions()}<button type="button">Clear</button>{/snippet}
    <input required aria-label="Amount" />
</InputWrapper>
