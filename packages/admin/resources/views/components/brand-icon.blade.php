@if (filled($brand = config('filament.brand')))
    <div @class([
        'text-xl font-bold tracking-tight filament-brand',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        {{
            str($brand)
                ->snake()
                ->upper()
                ->explode('_')
                ->map(fn (string $string) => str($string)->substr(0, 1))
                ->take(2)
                ->implode('')
        }}
    </div>
@endif
