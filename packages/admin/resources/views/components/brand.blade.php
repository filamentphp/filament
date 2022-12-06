@if (filled($brand = config('filament.brand')))
    <div @class([
        'filament-brand text-xl font-bold tracking-tight whitespace-nowrap',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        {{ $brand }}
    </div>
@endif
