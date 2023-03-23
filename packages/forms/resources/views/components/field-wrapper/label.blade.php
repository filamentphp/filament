@props([
    'error' => false,
    'isDisabled' => false,
    'isMarkedAsRequired' => true,
    'prefix' => null,
    'required' => false,
    'suffix' => null,
])

<label {{ $attributes->class(['filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse']) }}>
    {{ $prefix }}

    <span @class([
        'text-sm font-medium leading-4',
        'text-gray-700 dark:text-gray-300' => ! $error,
        'text-danger-700 dark:text-danger-400' => $error,
    ])>
        {{-- Deliberately poor formatting to ensure that the asterisk sticks to the final word in the label. --}}
        {{ $slot }}@if ($required && $isMarkedAsRequired && ! $isDisabled)<span class="whitespace-nowrap">
                <sup class="font-medium text-danger-700 dark:text-danger-400">*</sup>
            </span>
        @endif
    </span>

    {{ $suffix }}
</label>
