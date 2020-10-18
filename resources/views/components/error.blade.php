@props([
    'name',
])

@error($name) 
    <span {{ $attributes->merge(['class' => 'block text-red-600 text-sm font-medium']) }}>{{ $message }}</span> 
@enderror