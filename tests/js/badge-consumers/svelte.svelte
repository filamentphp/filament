<script lang="ts">
    import Badge from '../../../packages/support/resources/js/svelte/Badge.svelte'
    let element = $state<HTMLElement>()
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
