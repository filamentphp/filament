<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Radio from '../../../packages/support/resources/js/svelte/Radio.svelte'
    let delivery = $state<string | null>(null)
    const invalidType: ComponentProps<typeof Radio> = {
        // @ts-expect-error The primitive is always a radio.
        type: 'checkbox',
    }
    const invalidChildren: ComponentProps<typeof Radio> = {
        // @ts-expect-error Inputs cannot have children.
        children: () => '',
    }
    // @ts-expect-error Groups use native string values, not arrays.
    const invalidGroup: ComponentProps<typeof Radio> = {
        group: [],
    }
    void invalidType
    void invalidChildren
    void invalidGroup
</script>

<Radio
    bind:group={delivery}
    value="express"
    name="delivery"
    valid={false}
    required
    form="order"
    onchange={(event) => event.currentTarget.value}
/>
<Radio defaultChecked disabled />
