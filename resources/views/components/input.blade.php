@props([
    'type' => 'text',
    'field',
    'value' => null,
    'errorClasses' => $errors->has($field) ? ' border-red-600 motion-safe:animate-shake' : '',
])

<input type="{{ $type }}" field="{{ $field }}" value="{{ old($field, $value) }}" {{ $attributes->merge(['class' => 'block w-full rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50'.$errorClasses]) }}>