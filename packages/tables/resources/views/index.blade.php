@php
    use Filament\Schemas\Schema;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Components\TableToolbar;
    use Filament\Tables\Enums\TableFiltersPosition;
    use Filament\Tables\View\TablesRenderHook;

    $hasHeader = $hasHeader();
    $hasNonBulkToolbarAction = $hasNonBulkToolbarAction();
    $toolbarActions = $getVisibleToolbarActions();
    $headerVisibilityMode = $getHeaderVisibilityMode();
@endphp

<x-filament-tables::wrapper :table="$table">
        @include('filament-tables::components.parts.filters', ['table' => $table, 'part' => null, 'position' => TableFiltersPosition::Before])

        <div class="fi-ta-main">
            <div
                @if (! $hasHeader) x-cloak @endif
                x-show="@js($hasHeader) || @js($hasNonBulkToolbarAction) || (getSelectedRecordsCount() && @js(count($toolbarActions)))"
                wire:key="{{ $this->getId() }}.table.header.{{ $headerVisibilityMode }}"
                class="fi-ta-header-ctn"
            >
                @include('filament-tables::components.parts.header', ['table' => $table, 'part' => null])

                @include('filament-tables::components.parts.filters', ['table' => $table, 'part' => null, 'position' => TableFiltersPosition::Above])

                {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_BEFORE, scopes: static::class) }}

                @include('filament-tables::components.parts.toolbar', ['table' => $table, 'part' => TableToolbar::make()->container(Schema::make($this))])

                {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_AFTER) }}
            </div>

            @include('filament-tables::components.parts.selection-indicator', ['table' => $table, 'part' => null])

            @include('filament-tables::components.parts.filter-indicators', ['table' => $table, 'part' => null])

            @include('filament-tables::components.parts.content', ['table' => $table, 'part' => null])

            @include('filament-tables::components.parts.empty-state', ['table' => $table, 'part' => null])

            @include('filament-tables::components.parts.pagination', ['table' => $table, 'part' => null])

            @include('filament-tables::components.parts.filters', ['table' => $table, 'part' => null, 'position' => TableFiltersPosition::Below])
        </div>

        @include('filament-tables::components.parts.filters', ['table' => $table, 'part' => null, 'position' => TableFiltersPosition::After])
</x-filament-tables::wrapper>
