<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Icon from '../../../packages/support/resources/js/svelte/Icon.svelte'
    const invalidSize: ComponentProps<typeof Icon> = {
        // @ts-expect-error Sizes match `IconSize`.
        size: 'huge',
    }
    void invalidSize
</script>

<Icon
    size="lg"
    role="img"
    aria-label="Saved"
    onclick={(event) => event.currentTarget.focus()}
    ><svg viewBox="0 0 24 24"><path d="M4 12h16" /></svg></Icon
>
<Icon
    src="/icon.svg"
    srcset="/icon.svg 1x"
    decoding="async"
    alt="Saved"
    loading="lazy"
/>
