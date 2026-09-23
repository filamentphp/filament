<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Badge from '../../../packages/support/resources/js/svelte/Badge.svelte'
    let element = $state<HTMLElement>()
    // @ts-expect-error Delete buttons cannot be nested in links.
    const invalidLink: ComponentProps<typeof Badge> = {
        tag: 'a',
        onDelete: () => {},
    }
    // @ts-expect-error Delete buttons cannot be nested in buttons.
    const invalidButton: ComponentProps<typeof Badge> = {
        tag: 'button',
        onDelete: () => {},
    }
    const events: ComponentProps<typeof Badge> = {
        onclick: (event) => {
            // @ts-expect-error The root is an `HTMLElement`, not both a button and anchor.
            event.currentTarget.href
        },
    }
    void [invalidLink, invalidButton, events]
</script>

<Badge
    tag="button"
    type="submit"
    form="filters"
    bind:element
    tooltip="Save filters"
    keyBindings={['mod+shift+b']}
    onclick={(event) => event.preventDefault()}>Save</Badge
>
<Badge
    color="brand"
    size="xs"
    iconPosition="after"
    iconSize="lg"
    onDelete={(event) => event.preventDefault()}
    deleteLabel="Remove priority"
    deleteLoading>Priority{#snippet icon()}<svg />{/snippet}</Badge
>
