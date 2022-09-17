<td
    {{ $attributes->class([
        'filament-tables-reorder-cell w-4 px-4 whitespace-nowrap text-gray-500 transition group-hover:text-primary-500',
        'dark:text-gray-400 dark:group-hover:text-primary-400' => config('tables.dark_mode'),
    ]) }}
>
    <x-heroicon-o-menu class="block h-4 w-4" />
</td>
