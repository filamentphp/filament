<h2 {{ $attributes->class([
    'text-xl font-bold tracking-tight filament-tables-empty-state-heading',
    'dark:text-white' => config('tables.dark_mode'),
]) }}>
    {{ $slot }}
</h2>
