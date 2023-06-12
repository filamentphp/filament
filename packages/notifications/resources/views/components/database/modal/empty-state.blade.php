<div
    {{
        $attributes->class([
            'mx-auto my-6 flex flex-col items-center justify-center space-y-4 bg-white text-center',
            'dark:bg-gray-800' => config('notifications.dark_mode'),
        ])
    }}
>
    <div
        @class([
            'flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-500',
            'dark:bg-gray-700' => config('notifications.dark_mode'),
        ])
    >
        <x-heroicon-o-bell class="h-5 w-5" />
    </div>

    <div class="max-w-md space-y-1">
        <h2
            @class([
                'text-lg font-bold tracking-tight',
                'dark:text-white' => config('notifications.dark_mode'),
            ])
        >
            {{ __('notifications::database.modal.empty.heading') }}
        </h2>

        <p
            @class([
                'whitespace-normal text-sm font-medium text-gray-500',
                'dark:text-gray-400' => config('notifications.dark_mode'),
            ])
        >
            {{ __('notifications::database.modal.empty.description') }}
        </p>
    </div>
</div>
