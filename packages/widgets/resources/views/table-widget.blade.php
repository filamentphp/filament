<x-filament-widgets::widget class="fi-wi-table">
    {{ \Filament\Support\Facades\FilamentView::renderHook('widgets::table-widget.before', scopes: static::class) }}    
    
    {{ $this->table }}

    {{ \Filament\Support\Facades\FilamentView::renderHook('widgets::table-widget.after', scopes: static::class) }}
</x-filament-widgets::widget>
