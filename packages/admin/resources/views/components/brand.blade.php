@if (filled($brand = config('filament.brand')))
    <div class="text-xl font-bold tracking-tight filament-brand">
        {{ $brand }}
    </div>
@endif
