<div
    x-data="{ error: undefined }"
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: true)
            ->class(['filament-tables-text-input-column'])
    }}
>
    <input
        value="{{ $getState() }}"
        type="{{ $getType() }}"
        {!! $isDisabled() ? 'disabled' : null !!}
        {!! ($inputMode = $getInputMode()) ? "inputmode=\"{$inputMode}\"" : null !!}
        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
        {!! ($interval = $getStep()) ? "step=\"{$interval}\"" : null !!}
        x-on:change="
            response = await $wire.setColumnValue(@js($getName()), @js($recordKey), $event.target.value)
            error = response?.error ?? undefined
        "
        x-tooltip="error"
        x-bind:class="{
            'border-gray-300 dark:border-gray-600': ! error,
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: true)
                ->merge($getExtraInputAttributes(), escape: true)
                ->class(['ml-0.5 text-gray-900 block transition duration-75 rounded-lg shadow-sm focus:ring-primary-500 focus:ring-1 focus:ring-inset focus:border-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500'])
        }}
    />
</div>
