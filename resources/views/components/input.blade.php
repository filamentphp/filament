@props([
    'type' => 'text',
    'name',
    'errorClasses' => $errors->has($name) ? ' border-red-600' : '',
])

<input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}" {{ $attributes->merge(['class' => 'form-input w-full'.$errorClasses]) }}>