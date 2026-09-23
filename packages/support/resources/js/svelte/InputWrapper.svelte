<script lang="ts">
    import type { Snippet } from 'svelte'
    import type { HTMLAttributes } from 'svelte/elements'

    let {
        disabled = false,
        valid = true,
        inlinePrefix = false,
        inlineSuffix = false,
        prefix = null,
        suffix = null,
        prefixIcon,
        suffixIcon,
        prefixActions,
        suffixActions,
        class: className = '',
        children,
        element = $bindable(),
        ...attributes
    }: Omit<HTMLAttributes<HTMLDivElement>, 'class' | 'prefix'> & {
        disabled?: boolean
        valid?: boolean
        inlinePrefix?: boolean
        inlineSuffix?: boolean
        prefix?: string | Snippet | null
        suffix?: string | Snippet | null
        prefixIcon?: Snippet
        suffixIcon?: Snippet
        prefixActions?: Snippet
        suffixActions?: Snippet
        class?: string
        children?: Snippet
        element?: HTMLDivElement
    } = $props()

    const hasPrefixLabel = $derived(
        typeof prefix === 'function' || !!prefix?.trim(),
    )
    const hasSuffixLabel = $derived(
        typeof suffix === 'function' || !!suffix?.trim(),
    )
</script>

<div
    {...attributes}
    bind:this={element}
    class={[
        'fi-input-wrp',
        disabled && 'fi-disabled',
        !valid && 'fi-invalid',
        className,
    ]
        .filter(Boolean)
        .join(' ')}
>
    {#if hasPrefixLabel || prefixIcon || prefixActions}
        <div
            class={[
                'fi-input-wrp-prefix',
                'fi-input-wrp-prefix-has-content',
                inlinePrefix && 'fi-inline',
                hasPrefixLabel && 'fi-input-wrp-prefix-has-label',
            ]
                .filter(Boolean)
                .join(' ')}
        >
            {#if prefixActions}<div class="fi-input-wrp-actions">
                    {@render prefixActions()}
                </div>{/if}
            {@render prefixIcon?.()}
            {#if hasPrefixLabel}<span class="fi-input-wrp-label"
                    >{#if typeof prefix === 'function'}{@render prefix()}{:else}{prefix}{/if}</span
                >{/if}
        </div>
    {/if}
    <div class="fi-input-wrp-content-ctn">{@render children?.()}</div>
    {#if hasSuffixLabel || suffixIcon || suffixActions}
        <div
            class={[
                'fi-input-wrp-suffix',
                inlineSuffix && 'fi-inline',
                hasSuffixLabel && 'fi-input-wrp-suffix-has-label',
            ]
                .filter(Boolean)
                .join(' ')}
        >
            {#if hasSuffixLabel}<span class="fi-input-wrp-label"
                    >{#if typeof suffix === 'function'}{@render suffix()}{:else}{suffix}{/if}</span
                >{/if}
            {@render suffixIcon?.()}
            {#if suffixActions}<div class="fi-input-wrp-actions">
                    {@render suffixActions()}
                </div>{/if}
        </div>
    {/if}
</div>
