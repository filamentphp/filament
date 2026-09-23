<script lang="ts">
    import type { Snippet } from 'svelte'
    import type { HTMLAttributes } from 'svelte/elements'
    import Icon from './Icon.svelte'
    import type { IconSize } from '../components/icon'

    let {
        color = 'gray',
        heading,
        description,
        footer,
        controls,
        children,
        icon,
        iconColor,
        iconSize = 'lg',
        class: className = '',
        element = $bindable(),
        ...attributes
    }: Omit<HTMLAttributes<HTMLDivElement>, 'class' | 'children'> & {
        color?: string
        heading?: string | Snippet | null
        description?: string | Snippet | null
        footer?: string | Snippet | null
        controls?: string | Snippet | null
        children?: Snippet
        icon?: Snippet
        iconColor?: string
        iconSize?: IconSize
        class?: string
        element?: HTMLDivElement
    } = $props()
    const hasContent = (content: string | Snippet | null | undefined) =>
        typeof content === 'function' || Boolean(content?.trim())
    let actions = $derived(footer ?? children)
</script>

<div
    {...attributes}
    bind:this={element}
    class={[
        'fi-callout',
        color !== 'gray' && `fi-color fi-color-${color}`,
        className,
    ]
        .filter(Boolean)
        .join(' ')}
>
    {#if icon}<Icon
            size={iconSize}
            aria-hidden="true"
            class={[
                'fi-callout-icon',
                (iconColor ?? color) !== 'gray' &&
                    `fi-color fi-color-${iconColor ?? color}`,
            ]
                .filter(Boolean)
                .join(' ')}>{@render icon()}</Icon
        >{/if}
    {#if hasContent(heading) || hasContent(description) || hasContent(actions)}
        <div class="fi-callout-main">
            {#if hasContent(heading) || hasContent(description)}
                <div class="fi-callout-text">
                    {#if hasContent(heading)}<h4 class="fi-callout-heading">
                            {#if typeof heading === 'function'}{@render heading()}{:else}{heading}{/if}
                        </h4>{/if}
                    {#if hasContent(description)}<p
                            class="fi-callout-description"
                        >
                            {#if typeof description === 'function'}{@render description()}{:else}{description}{/if}
                        </p>{/if}
                </div>
            {/if}
            {#if hasContent(actions)}<div class="fi-callout-footer">
                    {#if typeof actions === 'function'}{@render actions()}{:else}{actions}{/if}
                </div>{/if}
        </div>
    {/if}
    {#if hasContent(controls)}<div class="fi-callout-controls">
            {#if typeof controls === 'function'}{@render controls()}{:else}{controls}{/if}
        </div>{/if}
</div>
