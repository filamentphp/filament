<button type="button"
    role="tab"
    class="text-sm leading-tight font-medium p-3 md:px-6 -mb-px border-r border-gray-200"
    :class="{ 'bg-white': tab === '{{ $id }}' }"
    :aria-selected="tab === '{{ $id }}'"
    aria-controls="{{ $id }}-tab" id="{{ $id }}"
    :tabindex="tab === '{{ $id }}' ? 0 : -1"
    @click="tab = '{{ $id }}'"
>
    {{ $slot }}
</button>
