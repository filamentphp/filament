<script lang="ts">
    import type { SVGAttributes } from 'svelte/elements'
    import {
        getLoadingIndicatorClasses,
        loadingIndicatorTrack,
        loadingIndicatorArc,
        type LoadingIndicatorSize,
    } from '../components/loading-indicator'

    let {
        size = 'md',
        class: className = '',
        ...attributes
    }: Omit<SVGAttributes<SVGSVGElement>, 'children' | 'class'> & {
        size?: LoadingIndicatorSize
        class?: string
    } = $props()
</script>

<svg
    fill="none"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    {...attributes}
    class={[getLoadingIndicatorClasses(size), className]
        .filter(Boolean)
        .join(' ')}
>
    <path
        clip-rule="evenodd"
        d={loadingIndicatorTrack}
        fill-rule="evenodd"
        fill="currentColor"
        opacity="0.2"
    ></path>
    <path d={loadingIndicatorArc} fill="currentColor"></path>
</svg>
