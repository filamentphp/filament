<button
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['fi-ta-reorder-handle cursor-move text-gray-500 transition group-hover:text-primary-500 dark:text-gray-400 dark:group-hover:text-primary-400'])
    }}
>
    <x-filament::icon
        alias="tables::reorder.handle"
        icon="heroicon-m-bars-3"
        class="block h-4 w-4"
    />
</button>
