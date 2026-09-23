<script lang="ts" generics="Value = unknown">
    import { untrack } from 'svelte'
    import type { HTMLSelectAttributes } from 'svelte/elements'

    let {
        inlinePrefix = false,
        element = $bindable(),
        defaultValue,
        value = $bindable(),
        children,
        class: className = '',
        ...attributes
    }: Omit<HTMLSelectAttributes, 'value' | 'defaultValue' | 'class'> & {
        inlinePrefix?: boolean
        element?: HTMLSelectElement
        value?: Value
        defaultValue?: Value
        class?: string
    } = $props()

    // Seed `multiple` with the whole default array, not the first selected option.
    untrack(() => {
        if (value === undefined && defaultValue !== undefined)
            value = defaultValue
    })
</script>

<select
    {...attributes}
    {...defaultValue === undefined ? {} : { defaultValue }}
    bind:this={element}
    bind:value
    class={[
        'fi-select-input',
        inlinePrefix && 'fi-select-input-has-inline-prefix',
        className,
    ]
        .filter(Boolean)
        .join(' ')}
>
    {@render children?.()}
</select>
