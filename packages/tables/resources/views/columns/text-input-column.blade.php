@php
    $alignClass = match ($getAlignment()) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    }
@endphp
<div
    x-data="{
        error: undefined,
        state: @js($getState()),
        isLoading: false
    }"
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-tables-text-input-column',
    ]) }}
>
    <input
        x-model="state"
        type="{{ $getType() }}"
        {!! $isDisabled() ? 'disabled' : null !!}
        {!! ($inputMode = $getInputMode()) ? "inputmode=\"{$inputMode}\"" : null !!}
        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
        {!! ($interval = $getStep()) ? "step=\"{$interval}\"" : null !!}
        x-on:change="
            isLoading = true
            response = await $wire.updateTableColumnState(@js($getName()), @js($recordKey), $event.target.value)
            error = response?.error ?? undefined
            if (! error) state = response
            isLoading = false
        "
        :readonly="isLoading"
        x-tooltip="error"
        {{ $attributes->merge($getExtraInputAttributes())->merge($getExtraAttributes())->class([
            'ml-0.5 text-gray-900 inline-block transition duration-75 rounded-lg shadow-sm focus:ring-primary-500 focus:ring-1 focus:ring-inset focus:border-primary-500 disabled:opacity-70 read-only:opacity-50',
            $alignClass,
            'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
        ]) }}
        x-bind:class="{
            'border-gray-300': ! error,
            'dark:border-gray-600': (! error) && @js(config('forms.dark_mode')),
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
    />
</div>
