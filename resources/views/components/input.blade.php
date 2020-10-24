@props([
    'type' => 'text',
    'name',
    'default' => null,
    'errorClasses' => $errors->has($name) ? ' border-red-600' : '',
])

<input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $default) }}" {{ $attributes->merge(['class' => 'form-input w-full'.$errorClasses]) }}>