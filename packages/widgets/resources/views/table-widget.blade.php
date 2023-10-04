<x-filament-widgets::widget class="fi-wi-table">
    {{ \Filament\Support\Facades\FilamentView::renderHook('widgets::table-widget.start', scopes: static::class) }}

    {{ $this->table }}

    {{ \Filament\Support\Facades\FilamentView::renderHook('widgets::table-widget.end', scopes: static::class) }}
</x-filament-widgets::widget>
