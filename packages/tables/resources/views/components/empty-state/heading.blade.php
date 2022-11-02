<h2 {{ $attributes->class([
    'filament-tables-empty-state-heading text-xl font-bold tracking-tight',
    'dark:text-white' => config('tables.dark_mode'),
]) }}>
    {{ $slot }}
</h2>
