<script lang="ts">
    import type { HTMLImgAttributes } from 'svelte/elements'
    import { getAvatarClasses } from '../components/avatar'

    let {
        alt = '',
        circular = true,
        size = 'md',
        class: className = '',
        ...attributes
    }: Omit<HTMLImgAttributes, 'class'> & {
        circular?: boolean
        size?: string
        class?: string
    } = $props()
</script>

<img
    {...attributes}
    {alt}
    class={[getAvatarClasses(circular, size), className]
        .filter(Boolean)
        .join(' ')}
/>
