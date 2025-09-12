@props([
    'disabled' => false,
    'prefix' => null,
    'required' => false,
    'suffix' => null,
    'tag' => 'label',
])

<{{ $tag }}
    {{ $attributes->class(['fi-fo-field-wrp-label inline-flex items-center gap-x-3']) }}
>
    {{ $prefix }}

    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
        {{-- Deliberately poor formatting to ensure that the asterisk sticks to the final word in the label. --}}
        {{ $slot }}@if ($required && (! $disabled))<sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
        @endif
    </span>

    {{ $suffix }}
</{{ $tag }}>
