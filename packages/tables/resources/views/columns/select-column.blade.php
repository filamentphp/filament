@php
    $isDisabled = $isDisabled();
@endphp

<div
    x-data="{
        error: undefined,
        state: @js($getState()),
        isLoading: false,
    }"
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-tables-select-column',
    ]) }}
>
    <select
        x-model="state"
        x-on:change="
            isLoading = true
            response = await $wire.updateTableColumnState(@js($getName()), @js($recordKey), $event.target.value)
            error = response?.error ?? undefined
            if (! error) state = response
            isLoading = false
        "
        @if ($isDisabled)
            disabled
        @else
            x-bind:disabled="isLoading"
        @endif
        x-tooltip="error"
        {{ $attributes->merge($getExtraInputAttributes())->merge($getExtraAttributes())->class([
            'ml-0.5 text-gray-900 inline-block transition duration-75 rounded-lg shadow-sm focus:ring-primary-500 focus:ring-1 focus:ring-inset focus:border-primary-500 disabled:opacity-70',
            'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
        ]) }}
        x-bind:class="{
            'border-gray-300': ! error,
            'dark:border-gray-600': (! error) && @js(config('forms.dark_mode')),
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
    >
        @unless ($isPlaceholderSelectionDisabled())
            <option value="">{{ $getPlaceholder() }}</option>
        @endif

        @foreach ($getOptions() as $value => $label)
            <option
                value="{{ $value }}"
                {!! $isOptionDisabled($value, $label) ? 'disabled' : null !!}
            >
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
