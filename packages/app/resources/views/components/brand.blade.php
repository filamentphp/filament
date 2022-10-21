@if (filled($brand = config('filament.brand')))
    <div class="filament-brand text-xl font-bold tracking-tight dark:text-white">
        {{ $brand }}
    </div>
@endif
