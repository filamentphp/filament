<div
    {{ $attributes->class([
        'filament-tables-reorder-handle text-gray-500 transition group-hover:text-primary-500',
        'dark:text-gray-400 dark:group-hover:text-primary-400' => config('tables.dark_mode'),
    ]) }}
>
    <x-heroicon-o-menu class="block h-4 w-4" />
</div>
