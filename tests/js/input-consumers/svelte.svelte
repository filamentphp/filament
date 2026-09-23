<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Input from '../../../packages/support/resources/js/svelte/Input.svelte'
    let value = $state<string | number>()
    let element = $state<HTMLInputElement>()
    const invalid: ComponentProps<typeof Input> = {
        // @ts-expect-error Inline flags are boolean.
        inlineSuffix: 'yes',
    }
    void invalid
</script>

<Input
    bind:element
    bind:value
    type="number"
    defaultValue={0}
    inlinePrefix
    readonly
    oninput={(event) => event.currentTarget.checkValidity()}
    aria-label="Amount"
/>
