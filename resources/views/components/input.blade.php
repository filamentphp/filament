@props([
    'name',
    'class' => null,
    'label' => null,
    'type' => 'text'
])

<input {{ $attributes }} name="{{ $name }}" type="{{ $type }}" class="{{ $class ?? 'form-input' }} @error($name) border-red-400 @enderror">
