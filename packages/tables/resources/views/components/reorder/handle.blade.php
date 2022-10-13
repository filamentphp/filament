<button
    type="button"
    {{ $attributes->class([
        'filament-tables-reorder-handle text-gray-500 cursor-move transition group-hover:text-primary-500',
        'dark:text-gray-400 dark:group-hover:text-primary-400' => config('tables.dark_mode'),
    ]) }}
>
    @svg('heroicon-o-bars-3', 'block h-4 w-4')
</button>
