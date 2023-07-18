<button
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['fi-fo-rich-editor-toolbar-btn flex h-8 min-w-[theme(spacing.8)] cursor-pointer items-center justify-center rounded-lg px-2 text-sm font-medium text-gray-700 transition duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:text-gray-300 dark:hover:bg-white/5 dark:focus:bg-white/5 [&.trix-active]:bg-gray-950/5 dark:[&.trix-active]:bg-white/5'])
    }}
>
    {{ $slot }}
</button>
