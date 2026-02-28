@if (filled($brand = config('filament.brand')))
    <div
        @class([
            'filament-brand text-xl font-bold leading-5 tracking-tight',
            'dark:text-white' => config('filament.dark_mode'),
        ])
    >
        {{ $brand }}
    </div>
@elseif (filled($brand = config('filament.brand_logo')))
    <a @class([
        'filament-brand leading-5 tracking-tight',
        'dark:text-white' => config('filament.dark_mode'),
    ]) href="{{ config("filament.home_url") }}">
        <img src="{{ $brand' }}" alt="Website Logo"
            width="{{ config('filament.brand_logo_width') }}">
    </a>
@endif
