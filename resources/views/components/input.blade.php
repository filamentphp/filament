@props([
    'type' => 'text',
    'name',
    'value' => null,
    'errorClasses' => $errors->has($name) ? ' border-red-600' : '',
])

<input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'block w-full rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50'.$errorClasses]) }}>