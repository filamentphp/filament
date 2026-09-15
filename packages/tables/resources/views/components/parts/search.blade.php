@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\View\TablesRenderHook;
@endphp

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE, scopes: static::class) }}

@if ($table->isSearchable())
    <x-filament-tables::search-field
        :debounce="$table->getSearchDebounce()"
        :on-blur="$table->isSearchOnBlur()"
        :placeholder="$table->getSearchPlaceholder()"
    />
@endif

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_AFTER, scopes: static::class) }}
