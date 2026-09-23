<script lang="ts">
    import type { HTMLAttributes } from 'svelte/elements'
    import {
        getLoadingSectionLayout,
        type LoadingSectionOptions,
    } from '../components/loading-section'

    let {
        columnSpan,
        columnStart,
        height,
        loadingLabel,
        class: className = '',
        style = '',
        element = $bindable(),
        ...attributes
    }: Omit<HTMLAttributes<HTMLDivElement>, 'class' | 'children' | 'style'> &
        LoadingSectionOptions & {
            class?: string
            style?: string
            element?: HTMLDivElement
        } = $props()
    const layout = $derived(
        getLoadingSectionLayout({ columnSpan, columnStart, height }),
    )
</script>

<div
    role="status"
    aria-busy="true"
    {...attributes}
    bind:this={element}
    class={[layout.className, className].filter(Boolean).join(' ')}
    style={`${Object.entries(layout.style)
        .map(([name, value]) => `${name}: ${value}`)
        .join('; ')}; ${style}`}
>
    <span class="fi-sr-only">{loadingLabel ?? 'Loading...'}</span>
</div>
