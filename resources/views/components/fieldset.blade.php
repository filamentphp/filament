@props([
    'legend' => null,
])

<fieldset class="border rounded border-grey-100 p-4 md:px-6">
    @if ($legend)
        <legend class="text-gray-500 text-sm leading-tight font-semibold px-2">{{ $legend }}</legend>
    @endif
    <div {{ $attributes }}>
        {{ $slot }}
    </div>
</fieldset>