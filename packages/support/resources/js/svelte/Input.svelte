<script lang="ts">
    import type { HTMLInputAttributes } from 'svelte/elements'

    let {
        inlinePrefix = false,
        inlineSuffix = false,
        element = $bindable(),
        value = $bindable(),
        class: className = '',
        ...attributes
    }: Omit<HTMLInputAttributes, 'children' | 'class' | 'value'> & {
        inlinePrefix?: boolean
        inlineSuffix?: boolean
        element?: HTMLInputElement
        value?: string | number | null
        class?: string
    } = $props()
</script>

<input
    {...attributes}
    bind:this={element}
    bind:value
    class={[
        'fi-input',
        inlinePrefix && 'fi-input-has-inline-prefix',
        inlineSuffix && 'fi-input-has-inline-suffix',
        className,
    ]
        .filter(Boolean)
        .join(' ')}
/>
