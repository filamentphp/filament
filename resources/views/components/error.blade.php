@props([
    'field',
])

@error($field) 
    <span {{ $attributes->merge(['class' => 'block text-red-700 text-sm leading-tight']) }}>{{ $message }}</span> 
@enderror