<script lang="ts">
    import Select from '../../../packages/support/resources/js/svelte/Select.svelte'
    let value = $state('drawing')
    let multiple = $state(['drawing'])
    let element = $state<HTMLSelectElement>()
</script>

<Select
    bind:element
    bind:value
    inlinePrefix
    onchange={(event) => event.currentTarget.selectedOptions}
    ><option value="drawing">Drawing</option></Select
>
<Select multiple bind:value={multiple} defaultValue={['drawing']}
    ><option value="drawing">Drawing</option></Select
>
