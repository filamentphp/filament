<button
    type="button"
    {{
        $attributes->class([
            'filament-tables-reorder-handle cursor-move text-gray-500 transition group-hover:text-primary-500',
            'dark:text-gray-400 dark:group-hover:text-primary-400' => config('tables.dark_mode'),
        ])
    }}
>
    <x-heroicon-s-menu class="block h-4 w-4" />
</button>
