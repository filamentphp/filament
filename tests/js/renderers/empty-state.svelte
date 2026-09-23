<script>
    import EmptyState from '../../../packages/support/resources/js/svelte/EmptyState.svelte'
    let { rich, withIcon, withActions, onAction, onElement, ...props } =
        $props()
    let element = $state()
    $effect(() => onElement(element))
</script>

{#snippet heading()}<strong>Projects</strong>{/snippet}
{#snippet description()}<em>Start here</em>{/snippet}
{#snippet icon()}<svg aria-hidden="true" viewBox="0 0 24 24"
        ><path d="M4 4h16v16H4z" /></svg
    >{/snippet}
{#snippet footer()}<input aria-label="Project name" name="project" /><button
        type="button"
        onclick={onAction}>Create project</button
    ><a href="#projects">Browse projects</a>{/snippet}
<EmptyState
    {...props}
    bind:element
    heading={rich ? heading : props.heading}
    description={rich ? description : props.description}
    icon={withIcon ? icon : undefined}
    footer={withActions ? footer : props.footer}
/>
