<p
    {{
        $attributes->class([
            'filament-notifications-date text-xs text-gray-500',
            'dark:text-gray-300' => config('notifications.dark_mode'),
        ])
    }}
>
    {{ $slot }}
</p>
