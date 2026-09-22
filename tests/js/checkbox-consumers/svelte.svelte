<script lang="ts">
    import type { ComponentProps } from 'svelte'
    import Checkbox from '../../../packages/support/resources/js/svelte/Checkbox.svelte'
    let checked = $state(false)
    let indeterminate = $state(true)
    const invalidType: ComponentProps<typeof Checkbox> = {
        // @ts-expect-error The primitive is always a checkbox.
        type: 'radio',
    }
    const invalidChildren: ComponentProps<typeof Checkbox> = {
        // @ts-expect-error Inputs cannot have children.
        children: () => '',
    }
    void invalidType
    void invalidChildren
</script>

<Checkbox
    bind:checked
    bind:indeterminate
    valid={false}
    name="terms"
    value="accepted"
    required
    form="profile"
    onchange={(event) => event.currentTarget.checked}
/>
<Checkbox defaultChecked disabled />
