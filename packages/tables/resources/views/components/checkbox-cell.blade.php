@props([
    'checked' => false,
    'onClick',
])

<th {{ $attributes->class(['w-4 px-4 whitespace-nowrap']) }}>
    <input
        {{ $checked ? 'checked' : null }}
        wire:click="{{ $onClick }}"
        type="checkbox"
        class="border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
    />
</th>
