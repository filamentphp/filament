@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'name' => null,
    'nameAttribute' => 'name',
])

<input
    @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
    type="checkbox"
    {{ $attributes->merge(array_merge([
        'class' => 'rounded text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-red-600 ' : 'border-gray-300'),
    ], $extraAttributes)) }}
/>
