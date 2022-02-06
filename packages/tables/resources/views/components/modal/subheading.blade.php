<h3 {{ $attributes->class([
    'text-gray-500 filament-tables-modal-subheading',
    'dark:text-gray-400' => config('tables.dark_mode'),
]) }}>
    {{ $slot }}
</h3>
