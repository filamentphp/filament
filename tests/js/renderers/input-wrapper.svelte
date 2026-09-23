<script>
    import InputWrapper from '../../../packages/support/resources/js/svelte/InputWrapper.svelte'
    import Icon from '../../../packages/support/resources/js/svelte/Icon.svelte'
    let { configuration, reportClick, reportWrapperClick, reportRef } = $props()
    let element = $state()
    $effect(() => reportRef(element))
</script>

{#snippet icon()}<Icon
        ><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h16" /></svg
        ></Icon
    >{/snippet}
{#snippet rich()}<strong>Price</strong>{/snippet}
{#snippet action()}<button type="button" onclick={reportClick}>Clear</button
    >{/snippet}
<InputWrapper
    bind:element
    onclick={reportWrapperClick}
    disabled={configuration.disabled}
    valid={configuration.valid}
    inlinePrefix={configuration.inlinePrefix}
    inlineSuffix={configuration.inlineSuffix}
    prefix={configuration.rich ? rich : configuration.prefix}
    suffix={configuration.suffix}
    prefixIcon={configuration.icons ? icon : undefined}
    suffixIcon={configuration.icons ? icon : undefined}
    prefixActions={configuration.actions ? action : undefined}
    suffixActions={configuration.actions ? action : undefined}
    data-state={configuration.changed ? 'changed' : undefined}
>
    <input class="fi-input" aria-label="Amount" required />
</InputWrapper>
