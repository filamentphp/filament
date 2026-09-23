<script lang="ts">
    import type { Snippet } from 'svelte'
    import type { HTMLAttributes } from 'svelte/elements'
    import Icon from './Icon.svelte'
    import type { IconSize } from '../components/icon'

    let {
        compact = false,
        contained = true,
        heading,
        headingTag = 'h2',
        description,
        footer,
        children,
        icon,
        iconColor = 'primary',
        iconSize = 'lg',
        class: className = '',
        element = $bindable(),
        ...attributes
    }: Omit<HTMLAttributes<HTMLDivElement>, 'class' | 'children'> & {
        compact?: boolean
        contained?: boolean
        heading?: string | Snippet | null
        headingTag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
        description?: string | Snippet | null
        footer?: string | Snippet | null
        children?: Snippet
        icon?: Snippet
        iconColor?: string
        iconSize?: IconSize
        class?: string
        element?: HTMLDivElement
    } = $props()
    let actions = $derived(footer ?? children)
</script>

<div
    {...attributes}
    bind:this={element}
    class={[
        'fi-empty-state',
        compact && 'fi-compact',
        !contained && 'fi-empty-state-not-contained',
        className,
    ]
        .filter(Boolean)
        .join(' ')}
>
    <div class="fi-empty-state-content">
        {#if icon}
            <div
                class={[
                    'fi-empty-state-icon-bg',
                    iconColor !== 'gray' && `fi-color fi-color-${iconColor}`,
                ]
                    .filter(Boolean)
                    .join(' ')}
            >
                <Icon
                    size={iconSize}
                    class={iconColor !== 'gray'
                        ? `fi-color fi-color-${iconColor}`
                        : ''}>{@render icon()}</Icon
                >
            </div>
        {/if}
        <div class="fi-empty-state-text-ctn">
            <svelte:element this={headingTag} class="fi-empty-state-heading"
                >{#if typeof heading === 'function'}{@render heading()}{:else}{heading}{/if}</svelte:element
            >
            {#if typeof description === 'function' || description?.trim()}
                <p class="fi-empty-state-description">
                    {#if typeof description === 'function'}{@render description()}{:else}{description}{/if}
                </p>
            {/if}
            {#if typeof actions === 'function' || actions?.trim()}
                <footer class="fi-empty-state-footer">
                    {#if typeof actions === 'function'}{@render actions()}{:else}{actions}{/if}
                </footer>
            {/if}
        </div>
    </div>
</div>
