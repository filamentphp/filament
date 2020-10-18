@props([
    'type' => 'text',
    'name',
    'hasError' => $errors->has($name) ? ' border-red-600' : '',
])

<input type="{{ $type }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'form-input w-full'.$hasError]) }}>