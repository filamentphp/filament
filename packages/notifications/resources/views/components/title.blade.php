<div
    @class([
        'filament-notifications-title flex h-6 items-center text-sm font-medium text-gray-900',
        'dark:text-gray-100' => config('notifications.dark_mode'),
    ])
>
    {{ $slot }}
</div>
