<div
    x-data="{ error: undefined, state: @js($getState()), loading: false }"
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-tables-checkbox-column',
    ]) }}
>
    <input
        x-model="state"
        {!! $isDisabled() ? 'disabled' : null !!}
        type="checkbox"
        x-on:change="
            loading = true
            response = await $wire.setColumnValue(@js($getName()), @js($recordKey), $event.target.checked)
            error = response?.error ?? undefined
            loading = false
        "
        x-tooltip="error"
        {{
            $attributes
                ->merge($getExtraInputAttributeBag()->getAttributes())
                ->class([
                    'ml-4 text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70',
                    'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                ])
        }}
        x-bind:class="{
            'opacity-70 pointer-events-none': loading,
            'border-gray-300': ! error,
            'dark:border-gray-600': (! error) && @js(config('forms.dark_mode')),
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
    />
</div>
