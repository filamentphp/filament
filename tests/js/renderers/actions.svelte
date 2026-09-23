<script>
    import Actions from '../../../packages/support/resources/js/svelte/Actions.svelte'
    let { onAction, onElement, ...props } = $props()
    let element = $state()
    $effect(() => onElement(element))
</script>

<Actions {...props} bind:element
    ><input aria-label="Project name" name="project" /><button
        type="submit"
        onclick={onAction}>Save</button
    ><button type="button" disabled>Unavailable</button><a href="#projects"
        >Projects</a
    ></Actions
>
