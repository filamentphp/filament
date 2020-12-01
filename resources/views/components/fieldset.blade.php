@props([
    'cols' => 1,
    'legend' => null,
])

<fieldset {{ $attributes->merge(['class' => 'border rounded border-grey-100 p-4 md:px-6 grid grid-cols-1 gap-2 lg:gap-6 lg:grid-cols-'.$cols]) }}>
    @if ($legend)
        <legend class="text-gray-500 text-sm leading-tight font-semibold px-2">{{ $legend }}</legend>
    @endif
    {{ $slot }}
</fieldset>