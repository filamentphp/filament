<script lang="ts">
    import type { Snippet } from 'svelte'
    import type { HTMLAttributes } from 'svelte/elements'
    import { getActionsClasses } from '../components/actions'

    let {
        alignment,
        fullWidth,
        children,
        class: className = '',
        element = $bindable(),
        ...attributes
    }: Omit<HTMLAttributes<HTMLDivElement>, 'class' | 'children'> & {
        alignment?: string | null
        fullWidth?: boolean
        children?: Snippet
        class?: string
        element?: HTMLDivElement
    } = $props()
</script>

<div
    {...attributes}
    bind:this={element}
    class={[getActionsClasses(alignment, fullWidth), className]
        .filter(Boolean)
        .join(' ')}
>
    {@render children?.()}
</div>
