<button
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['filament-tables-reorder-handle cursor-move text-gray-500 transition group-hover:text-primary-500 dark:text-gray-400 dark:group-hover:text-primary-400'])
    }}
>
    <x-filament::icon
        name="heroicon-o-bars-3"
        alias="tables::reorder.handle"
        size="h-4 w-4"
        class="block"
    />
</button>
