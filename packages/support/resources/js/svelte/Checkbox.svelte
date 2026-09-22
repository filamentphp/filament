<script lang="ts">
    import type { HTMLInputAttributes } from 'svelte/elements'

    let {
        valid = true,
        checked = $bindable(),
        indeterminate = $bindable(),
        class: className = '',
        ...attributes
    }: Omit<HTMLInputAttributes, 'type' | 'class' | 'children'> & {
        valid?: boolean
        class?: string
    } = $props()
</script>

<input
    {...attributes}
    type="checkbox"
    bind:checked
    bind:indeterminate
    class={['fi-checkbox-input', valid ? 'fi-valid' : 'fi-invalid', className]
        .filter(Boolean)
        .join(' ')}
/>
