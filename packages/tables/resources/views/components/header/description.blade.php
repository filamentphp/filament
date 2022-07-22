<p {{ $attributes->class(['filament-tables-header-description']) }}>
    'text-gray-900 filament-tables-header-description',
    'dark:text-gray-100' => config('filament.dark_mode')
]) }}>
    {{ $slot }}
</p>
