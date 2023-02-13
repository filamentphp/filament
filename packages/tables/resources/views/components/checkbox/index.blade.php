@props([
    'label' => null
])

<label>
    <input
        {{ $attributes->class([
            'block border-gray-300 rounded shadow-sm text-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50',
            'dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-primary-600 dark:checked:border-primary-600' => config('tables.dark_mode'),
        ]) }}
        wire:loading.attr="disabled"
        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
        type="checkbox"
    />

    <span class="sr-only">
        {{ $label }}
    </span>
</label>
