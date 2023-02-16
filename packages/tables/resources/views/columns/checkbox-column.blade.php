<div
    x-data="{
        error: undefined,
        state: @js((bool) $getState()),
        isLoading: false
    }"
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class([
        'filament-tables-checkbox-column',
    ]) }}
>
    <input
        x-model="state"
        @disabled($isDisabled())
        type="checkbox"
        x-on:change="
            isLoading = true
            response = await $wire.updateTableColumnState(@js($getName()), @js($recordKey), $event.target.checked)
            error = response?.error ?? undefined
            isLoading = false
        "
        x-tooltip="error"
        {{
            $attributes
                ->merge($getExtraInputAttributes(), escape: false)
                ->class(['ml-4 text-primary-600 transition duration-75 rounded shadow-sm outline-none text-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500'])
        }}
        x-bind:class="{
            'opacity-70 pointer-events-none': isLoading,
            'border-gray-300 dark:border-gray-600': ! error,
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
    />
</div>
