<h2 {{ $attributes->class([
    'text-xl font-bold tracking-tight filament-tables-modal-heading',
    'dark:text-white' => config('tables.dark_mode'),
]) }}>
    {{ $slot }}
</h2>
