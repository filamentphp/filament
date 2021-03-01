@props([
    'title'
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <h3 class="text-lg leading-tight font-medium">
        {{ $title }}
    </h3>

    {{ $slot }}
</div>