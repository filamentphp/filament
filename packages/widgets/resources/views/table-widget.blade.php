@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Widgets\View\WidgetsRenderHook;
@endphp

<x-filament-widgets::widget class="fi-wi-table">
    {{ FilamentView::renderHook(WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

    {{ $this->table ?? null }}

    {{ FilamentView::renderHook(WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
