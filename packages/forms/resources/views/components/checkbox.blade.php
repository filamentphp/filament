@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'name' => null,
    'nameAttribute' => 'name',
])

<input
    {!! $name ? "{$nameAttribute}=\"{$name}\"" : null !!}
    type="checkbox"
    {{ $attributes->merge(array_merge([
        'class' => 'rounded text-secondary shadow-sm focus:border-secondary focus:ring focus:ring-secondary-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-danger-600 ' : 'border-gray-300'),
    ], $extraAttributes)) }}
/>
