<td {{ $attributes->class(['w-full px-4 py-4 animate-pulse']) }}>
    <div @class([
        'h-4 bg-gray-300 rounded-md',
        'dark:bg-gray-600' => config('tables.dark_mode'),
    ])></div>
</td>
