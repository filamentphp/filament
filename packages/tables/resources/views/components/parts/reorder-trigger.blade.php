@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\View\TablesRenderHook;
@endphp

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_BEFORE, scopes: static::class) }}

@if ($table->isReorderable())
    {{ $table->getReorderRecordsTriggerAction($table->isReordering()) }}
@endif

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, scopes: static::class) }}
