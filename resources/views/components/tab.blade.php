@props([
    'id',
])

<button type="button"
    aria-controls="{{ $id }}-tab"
    x-bind:aria-selected="tab === '{{ $id }}'"
    x-on:click="tab = '{{ $id }}'"
    role="tab"
    x-bind:tabindex="tab === '{{ $id }}' ? 0 : -1"
    class="text-sm leading-tight font-medium p-3 md:px-6 -mb-px border-r border-gray-200"
    x-bind:class="{ 'bg-white': tab === '{{ $id }}' }"
>
    {{ $slot }}
</button>
