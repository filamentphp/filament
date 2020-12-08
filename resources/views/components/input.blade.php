@props([
    'type' => 'text',
    'field',
    'errorClasses' => $errors->has($field) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300',
])

<input type="{{ $type }}" value="{{ old($field, $value ?? null) }}" {{ $attributes->merge(['class' => 'block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 '.$errorClasses]) }}>