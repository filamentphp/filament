<script lang="ts">
    import type { HTMLInputAttributes } from 'svelte/elements'

    let {
        valid = true,
        group = $bindable(),
        value = 'on',
        class: className = '',
        onchange,
        ...attributes
    }: Omit<HTMLInputAttributes, 'type' | 'class' | 'children' | 'value'> & {
        valid?: boolean
        group?: string | null
        value?: string
        class?: string
    } = $props()
</script>

<input
    {...{
        ...attributes,
        ...(group === undefined
            ? {}
            : { checked: group === value, defaultChecked: group === value }),
    }}
    type="radio"
    {value}
    onchange={(event) => {
        if (group !== undefined) group = event.currentTarget.value
        onchange?.(event)
    }}
    class={['fi-radio-input', !valid && 'fi-invalid', className]
        .filter(Boolean)
        .join(' ')}
/>
