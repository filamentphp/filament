@props([
    'field',
    'errorClasses' => $errors->has($field) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300',
])

<textarea {{ $attributes->merge(['class' => 'block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 '.$errorClasses]) }}>{{ old($field, $value ?? null) }}</textarea>