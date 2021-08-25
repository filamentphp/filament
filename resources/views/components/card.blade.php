@props([
    'expanded' => false,
])

<div {{ $attributes->class([
    'bg-white rounded p-4 md:p-6',
    'col-span-full' => $expanded,
]) }}>
    {{ $slot }}
</div>
