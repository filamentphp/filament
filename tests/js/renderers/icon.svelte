<script>
    import Icon from '../../../packages/support/resources/js/svelte/Icon.svelte'
    let { empty, changed, ...attributes } = $props()
</script>

{#if empty}
    <Icon {...attributes} />
{:else}
    <Icon {...attributes}
        ><svg
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            aria-hidden="true"
            ><path d={changed ? 'M6 6l12 12M6 18L18 6' : 'M4 12h16'}
            ></path></svg
        ></Icon
    >
{/if}
