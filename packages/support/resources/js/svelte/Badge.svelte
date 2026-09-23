<script lang="ts">
    import type { Snippet } from 'svelte'
    import type {
        DOMAttributes,
        HTMLAttributes,
        HTMLAnchorAttributes,
        HTMLButtonAttributes,
    } from 'svelte/elements'
    import { getBadgeClasses, type BadgeOptions } from '../components/badge'
    import { interactive } from '../components/interactive'
    import Icon from './Icon.svelte'
    import LoadingIndicator from './LoadingIndicator.svelte'
    let {
        tag = 'span',
        color = 'primary',
        size = 'md',
        icon,
        iconPosition = 'before',
        iconSize = 'sm',
        loading = false,
        disabled = false,
        tooltip,
        keyBindings,
        deleteLabel = 'Delete',
        deleteLoading = false,
        onDelete,
        children,
        class: className = '',
        element = $bindable(),
        style,
        onclick,
        href,
        type = 'button',
        tabindex,
        ...attributes
    }: Omit<
        HTMLAnchorAttributes & HTMLButtonAttributes,
        keyof DOMAttributes<HTMLElement> | 'children' | 'color'
    > &
        Omit<HTMLAttributes<HTMLElement>, 'children' | 'color'> &
        Omit<BadgeOptions, 'tag'> & {
            children?: Snippet
            icon?: Snippet
            element?: HTMLElement
        } & (
            | { tag?: 'span'; onDelete?: (event: MouseEvent) => void }
            | { tag: 'a' | 'button'; onDelete?: never }
        ) = $props()
    let blocked = $derived(disabled || loading)
    let keyBindingsSignature = $derived(JSON.stringify(keyBindings ?? []))
    let validatedTag = $derived.by(() => {
        if (onDelete && tag !== 'span')
            throw new Error('Deletable badges must use tag="span".')
        return tag
    })
    $effect(() => {
        if (element)
            return interactive(element, {
                tooltip,
                keyBindings: JSON.parse(keyBindingsSignature),
                disabled: blocked,
            })
    })
</script>

{#snippet indicator()}
    {#if loading}<LoadingIndicator size={iconSize} />{:else if icon}<Icon
            size={iconSize}
            aria-hidden="true">{@render icon()}</Icon
        >{/if}
{/snippet}
<svelte:element
    this={validatedTag}
    {...attributes}
    bind:this={element}
    href={tag === 'a' && !blocked ? href : undefined}
    role={attributes.role ??
        (tag === 'a' && href != null && blocked ? 'link' : undefined)}
    type={tag === 'button' ? type : undefined}
    disabled={tag === 'button' && blocked && !tooltip ? true : undefined}
    aria-disabled={blocked || undefined}
    aria-busy={loading || undefined}
    tabindex={blocked && tooltip ? (tabindex ?? 0) : tabindex}
    style={`${style ?? ''};${blocked && tooltip ? 'pointer-events: auto' : ''}`}
    class={[getBadgeClasses({ color, size, disabled, loading }), className]
        .filter(Boolean)
        .join(' ')}
    onclick={(event: MouseEvent) => {
        if (blocked) {
            event.preventDefault()
            event.stopPropagation()
            return
        }
        onclick?.(
            event as MouseEvent & { currentTarget: EventTarget & HTMLElement },
        )
    }}
>
    {#if iconPosition === 'before'}{@render indicator()}{/if}
    <span class="fi-badge-label-ctn"
        ><span class="fi-badge-label">{@render children?.()}</span></span
    >
    {#if onDelete}
        <button
            type="button"
            class="fi-badge-delete-btn"
            disabled={blocked || deleteLoading}
            aria-busy={deleteLoading || undefined}
            onclick={(event) => {
                event.stopPropagation()
                if (!blocked && !deleteLoading) onDelete?.(event)
            }}
        >
            {#if deleteLoading}<LoadingIndicator size="xs" />{:else}<span
                    class="fi-badge-delete-btn-icon"
                    aria-hidden="true"
                ></span>{/if}
            <span class="fi-sr-only"
                >{deleteLabel.trim() ? deleteLabel : 'Delete'}</span
            >
        </button>
    {:else if iconPosition === 'after'}{@render indicator()}{/if}
</svelte:element>
