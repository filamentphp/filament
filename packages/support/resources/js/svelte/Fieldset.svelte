<script lang="ts">
    import type { Snippet } from 'svelte'
    import type { HTMLFieldsetAttributes } from 'svelte/elements'

    let {
        contained = true,
        label = null,
        labelHidden = false,
        required = false,
        class: className = '',
        children,
        ...attributes
    }: Omit<HTMLFieldsetAttributes, 'class'> & {
        contained?: boolean
        label?: string | Snippet | null
        labelHidden?: boolean
        required?: boolean
        class?: string
        children?: Snippet
    } = $props()
</script>

<fieldset
    {...attributes}
    class={[
        'fi-fieldset',
        labelHidden && 'fi-fieldset-label-hidden',
        !contained && 'fi-fieldset-not-contained',
        className,
    ]
        .filter(Boolean)
        .join(' ')}
>
    {#if typeof label === 'function' || label?.trim()}
        <legend>
            {#if typeof label === 'function'}{@render label()}{:else}{label}{/if}{#if required}<sup
                    class="fi-fieldset-label-required-mark">*</sup
                >{/if}
        </legend>
    {/if}
    {@render children?.()}
</fieldset>
