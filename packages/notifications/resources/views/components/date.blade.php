<p {{ $attributes->class([
    'filament-notifications-date text-xs text-gray-500',
    'dark:text-gray-300' => config('filament-notifications.dark_mode'),
]) }}>
    {{ $slot }}
</p>
