@props([
    'title'
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <h3 class="text-lg leading-6 font-medium text-gray-900">
        {{ $title }}
    </h3>

    {{ $slot }}
</div>