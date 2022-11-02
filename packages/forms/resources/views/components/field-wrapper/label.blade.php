@props([
    'error' => false,
    'prefix' => null,
    'required' => false,
    'suffix' => null,
])

<label {{ $attributes->class(['filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse']) }}>
    {{ $prefix }}

    <span @class([
        'text-sm font-medium leading-4',
        'text-gray-700' => ! $error,
        'dark:text-gray-300' => (! $error) && config('forms.dark_mode'),
        'text-danger-700' => $error,
    ])>
        {{ $slot }}

        @if ($required)
            <sup class="font-medium text-danger-700">*</sup>
        @endif
    </span>

    {{ $suffix }}
</label>
