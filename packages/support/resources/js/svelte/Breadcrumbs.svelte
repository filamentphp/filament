<script lang="ts">
    import type { Snippet } from 'svelte'
    import type { HTMLAnchorAttributes, HTMLAttributes } from 'svelte/elements'
    import {
        breadcrumbSeparator,
        breadcrumbSeparatorRtl,
    } from '../components/breadcrumbs'

    type BreadcrumbItem = Omit<
        HTMLAnchorAttributes,
        'children' | 'class' | 'href'
    > & { label: string; href?: string; class?: string }

    let {
        breadcrumbs = [],
        separator,
        separatorRtl,
        class: className = '',
        'aria-label': ariaLabel = 'Breadcrumbs',
        ...attributes
    }: Omit<HTMLAttributes<HTMLElement>, 'children' | 'class'> & {
        breadcrumbs?: readonly BreadcrumbItem[]
        separator?: Snippet
        separatorRtl?: Snippet
        class?: string
    } = $props()
</script>

<nav
    {...attributes}
    aria-label={ariaLabel}
    class={['fi-breadcrumbs', className].filter(Boolean).join(' ')}
>
    <ol class="fi-breadcrumbs-list">
        {#each breadcrumbs as { label, class: itemClass, ...linkAttributes }, index}
            <li class="fi-breadcrumbs-item">
                {#if index > 0}
                    {#if separator}
                        <span
                            aria-hidden="true"
                            class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-ltr"
                            >{@render separator()}</span
                        >
                    {:else}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                            data-slot="icon"
                            class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-ltr"
                            ><path
                                fill-rule="evenodd"
                                d={breadcrumbSeparator}
                                clip-rule="evenodd"
                            /></svg
                        >
                    {/if}
                    {#if separatorRtl}
                        <span
                            aria-hidden="true"
                            class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-rtl"
                            >{@render separatorRtl()}</span
                        >
                    {:else}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                            data-slot="icon"
                            class="fi-icon fi-size-md fi-breadcrumbs-item-separator fi-rtl"
                            ><path
                                fill-rule="evenodd"
                                d={breadcrumbSeparatorRtl}
                                clip-rule="evenodd"
                            /></svg
                        >
                    {/if}
                {/if}
                <svelte:element
                    this={linkAttributes.href === undefined ? 'span' : 'a'}
                    {...linkAttributes}
                    aria-current={index === breadcrumbs.length - 1
                        ? 'page'
                        : undefined}
                    class={['fi-breadcrumbs-item-label', itemClass]
                        .filter(Boolean)
                        .join(' ')}>{label}</svelte:element
                >
            </li>
        {/each}
    </ol>
</nav>
