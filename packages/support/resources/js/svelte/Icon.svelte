<script lang="ts">
    import type { HTMLAttributes, HTMLImgAttributes } from 'svelte/elements'
    import { getIconClasses, type IconSize } from '../components/icon'

    let {
        src,
        alt = '',
        size = 'md',
        class: className = '',
        children,
        ...attributes
    }: Omit<HTMLAttributes<HTMLSpanElement | HTMLImageElement>, 'class'> &
        Omit<HTMLImgAttributes, keyof HTMLAttributes<HTMLImageElement>> & {
            size?: IconSize
            class?: string
        } = $props()
</script>

{#if src}
    <img
        {...attributes}
        {src}
        {alt}
        class={[getIconClasses(size), className].filter(Boolean).join(' ')}
    />
{:else if children}
    <span
        {...attributes}
        class={[getIconClasses(size), className].filter(Boolean).join(' ')}
        >{@render children()}</span
    >
{/if}
