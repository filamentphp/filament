<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Actions from '../../../packages/support/resources/js/svelte/Actions.svelte'
    let element = $state<HTMLDivElement>()
    // @ts-expect-error `fullWidth` must be boolean.
    const invalid: ComponentProps<typeof Actions> = { fullWidth: 'yes' }
    void invalid
</script>

<Actions
    bind:element
    alignment="right"
    fullWidth
    title="Actions"
    onclick={(event) => event.currentTarget.focus()}
    ><button type="submit">Save</button></Actions
>
