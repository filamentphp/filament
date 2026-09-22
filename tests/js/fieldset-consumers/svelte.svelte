<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Fieldset from '../../../packages/support/resources/js/svelte/Fieldset.svelte'
    const invalidContained: ComponentProps<typeof Fieldset> = {
        // @ts-expect-error `contained` must be a boolean.
        contained: 'false',
    }
    const invalidDisabled: ComponentProps<typeof Fieldset> = {
        // @ts-expect-error `disabled` must be a boolean.
        disabled: 'false',
    }
    void invalidContained
    void invalidDisabled
</script>

<Fieldset
    required
    labelHidden
    contained={false}
    disabled
    name="address"
    form="profile"
    onchange={(event) => event.currentTarget.checkValidity()}
>
    {#snippet label()}<strong>Address</strong>{/snippet}
    <input aria-label="Street" />
</Fieldset>
