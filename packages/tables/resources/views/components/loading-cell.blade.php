<td {{ $attributes->class(['w-full animate-pulse px-4 py-4']) }}>
    <div
        @class([
            'h-4 rounded-md bg-gray-300',
            'dark:bg-gray-600' => config('tables.dark_mode'),
        ])
    ></div>
</td>
