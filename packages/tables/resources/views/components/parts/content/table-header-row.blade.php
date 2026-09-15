@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Filament\Tables\View\TablesIconAlias;
    use Filament\Tables\View\TablesRenderHook;
    use Illuminate\Contracts\Support\Htmlable;
@endphp

<tr>
    @if (count($records))
        @if ($isReordering)
            <th></th>
        @else
            @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::BeforeCells)
                @if ($recordActionsColumnLabel)
                    <th scope="col" class="fi-ta-header-cell">
                        {{ $recordActionsColumnLabel }}
                    </th>
                @else
                    <th
                        aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                        scope="col"
                        class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                    ></th>
                @endif
            @endif

            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                <th scope="col" class="fi-ta-cell fi-ta-selection-cell">
                    @if (($maxSelectableRecords !== 1) && (! $selectsGroupsOnly))
                        @include('filament-tables::components.parts.content.page-checkbox', ['isStacked' => false])
                    @endif
                </th>
            @endif

            @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::BeforeColumns)
                @if ($recordActionsColumnLabel)
                    <th scope="col" class="fi-ta-header-cell">
                        {{ $recordActionsColumnLabel }}
                    </th>
                @else
                    <th
                        aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                        scope="col"
                        class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                    ></th>
                @endif
            @endif
        @endif
    @endif

    @php
        $hasHeaderCellRenderHook = FilamentView::hasRenderHook(TablesRenderHook::HEADER_CELL, scopes: static::class);
    @endphp

    @foreach ($columns as $column)
        @if ($hasHeaderCellRenderHook && filled($headerCellView = FilamentView::renderHook(TablesRenderHook::HEADER_CELL, scopes: static::class, data: [
                 'column' => $column,
                 'isReordering' => $isReordering,
             ])))
            {{ $headerCellView }}
        @else
            @php
                $columnName = $column->getName();
                $columnLabel = $column->getLabel();
                $columnAlignment = $column->getAlignment();
                $columnWidth = $column->getWidth();
                $isColumnActivelySorted = $table->getSortColumn() === $column->getName();
                $isColumnSortable = $column->isSortable() && (! $isReordering);

                // A custom label may contain interactive elements, which are invalid inside a native `<button>`, so a `<span>` with button semantics is used instead.
                $columnSortControlTag = ($columnLabel instanceof Htmlable) ? 'span' : 'button';

                $columnHeaderTooltip = $column->getHeaderTooltip();
                $columnHeaderTooltipAttribute = ($columnHeaderTooltip instanceof Htmlable)
                    ? 'x-tooltip.html'
                    : 'x-tooltip';
            @endphp

            <th
                @if ($isColumnSortable)
                    aria-sort="{{ $isColumnActivelySorted ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}"
                @endif
                scope="col"
                {{
                    $column->getExtraHeaderAttributeBag()
                        ->class([
                            'fi-ta-header-cell',
                            'fi-ta-header-cell-' . str($columnName)->camel()->kebab(),
                            'fi-growable' => blank($columnWidth) && $column->canGrow(default: false),
                            'fi-grouped' => $column->getGroup(),
                            'fi-wrapped' => $column->canHeaderWrap(),
                            'fi-ta-header-cell-sorted' => $isColumnActivelySorted,
                            ((($columnAlignment = $column->getAlignment()) instanceof Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
                            (filled($columnHiddenFrom = $column->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
                            (filled($columnVisibleFrom = $column->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
                        ])
                        ->style([
                            ('width: ' . e($columnWidth)) => filled($columnWidth),
                        ])
                }}
            >
                @if ($isColumnSortable)
                    <{{ $columnSortControlTag }}
                        @if ($columnSortControlTag === 'button')
                            type="button"
                        @else
                            role="button"
                            tabindex="0"
                            x-on:keydown.enter.prevent.stop="$wire.sortTable('{{ $columnName }}')"
                            x-on:keydown.space.prevent.stop="$wire.sortTable('{{ $columnName }}')"
                        @endif
                        wire:click="sortTable('{{ $columnName }}')"
                        wire:loading.attr="disabled"
                        wire:target="sortTable('{{ $columnName }}')"
                        class="fi-ta-header-cell-sort-btn"
                    >
                        @if (filled($columnHeaderTooltip))
                            <span
                                {{ $columnHeaderTooltipAttribute }}="{
                                    content: @js($columnHeaderTooltip),
                                    theme: $store.theme,
                                }"
                                class="fi-ta-header-cell-tooltip"
                            >
                                {{ $columnLabel }}
                            </span>
                        @else
                            {{ $columnLabel }}
                        @endif

                        {{
                            \Filament\Support\generate_icon_html(($isColumnActivelySorted && $sortDirection === 'asc') ? Heroicon::ChevronUp : Heroicon::ChevronDown, alias: match (true) {
                                $isColumnActivelySorted && ($sortDirection === 'asc') => TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON,
                                $isColumnActivelySorted && ($sortDirection === 'desc') => TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON,
                                default => TablesIconAlias::HEADER_CELL_SORT_BUTTON,
                            }, attributes: (new FilamentComponentAttributeBag([
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => true,
                                'wire:target' => "sortTable('{$columnName}')",
                            ])))
                        }}

                        {{
                            \Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => "sortTable('{$columnName}')",
                            ]))
                        }}
                    </{{ $columnSortControlTag }}>
                @else
                    @if (filled($columnHeaderTooltip))
                        <span
                            {{ $columnHeaderTooltipAttribute }}="{
                                content: @js($columnHeaderTooltip),
                                theme: $store.theme,
                            }"
                            class="fi-ta-header-cell-tooltip"
                        >
                            {{ $columnLabel }}
                        </span>
                    @else
                        {{ $columnLabel }}
                    @endif
                @endif
            </th>
        @endif
    @endforeach

    @if ((! $isReordering) && count($records))
        @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::AfterColumns)
            @if ($recordActionsColumnLabel)
                <th scope="col" class="fi-ta-header-cell fi-align-end">
                    {{ $recordActionsColumnLabel }}
                </th>
            @else
                <th
                    aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                    scope="col"
                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                ></th>
            @endif
        @endif

        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
            <th scope="col" class="fi-ta-cell fi-ta-selection-cell">
                @if (($maxSelectableRecords !== 1) && (! $selectsGroupsOnly))
                    @include('filament-tables::components.parts.content.page-checkbox', ['isStacked' => false])
                @endif
            </th>
        @endif

        @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::AfterCells)
            @if ($recordActionsColumnLabel)
                <th scope="col" class="fi-ta-header-cell fi-align-end">
                    {{ $recordActionsColumnLabel }}
                </th>
            @else
                <th
                    aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                    scope="col"
                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                ></th>
            @endif
        @endif
    @endif
</tr>
