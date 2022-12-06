@if (filled($brand = config('filament.brand')))
    <div @class([
        'filament-brand whitespace-nowrap text-xl font-bold tracking-tight',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        {{ $brand }}
    </div>
@endif
