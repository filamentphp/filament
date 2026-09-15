@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Filament\Tables\View\TablesIconAlias;
@endphp

@if (! $isGroupsOnly)
    <tr
        wire:key="{{ $this->getId() }}.table.records.{{ $recordKey }}"
        {{ $isReordering ? 'x-sortable-handle' : null }}
        {!! $isReordering ? 'x-sortable-item="' . e($recordKey) . '"' : null !!}
        x-bind:class="{
            {{ $group?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . \Illuminate\Support\Js::from($recordGroupTitle) . '),' : '' }}
            'fi-selected': @js($recordIsSelectable) && isRecordSelected(@js($recordKey)),
        }"
        @class([
            'fi-ta-row',
            'fi-clickable' => $recordAction || $recordUrl,
            'fi-striped' => $isStriped && $isRecordRowStriped,
            ...$table->getRecordClasses($record),
        ])
    >
        @if ($isReordering)
            <td class="fi-ta-cell">
                <button
                    aria-label="{{ __('filament-tables::table.actions.reorder_record.label', ['key' => $recordKey]) }}"
                    class="fi-ta-reorder-handle fi-icon-btn"
                    type="button"
                >
                    {{ \Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE) }}
                </button>
            </td>
        @endif

        @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::BeforeCells && (! $isReordering))
            <td class="fi-ta-cell">
                <div
                    @class([
                        'fi-ta-actions',
                        match ($recordActionsAlignment) {
                            Alignment::Center => 'fi-align-center',
                            Alignment::Start, Alignment::Left => 'fi-align-start',
                            Alignment::Between, Alignment::Justify => 'fi-align-between',
                            Alignment::End, Alignment::Right => '',
                            default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                        },
                    ])
                >
                    @foreach ($recordActions as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </td>
        @endif

        @if ($isSelectionEnabled && ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) && (! $isReordering))
            <td class="fi-ta-cell fi-ta-selection-cell">
                @if ($recordIsSelectable)
                    <input
                        aria-label="{{ __('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey]) }}"
                        type="checkbox"
                        @if ($isSelectionDisabled)
                            disabled
                        @elseif ($maxSelectableRecords && ($maxSelectableRecords !== 1))
                            x-bind:disabled="! areRecordsToggleable([@js($recordKey)])"
                        @endif
                        value="{{ $recordKey }}"
                        x-on:click="toggleSelectedRecord(@js($recordKey))"
                        x-bind:checked="isRecordSelected(@js($recordKey)) ? 'checked' : null"
                        data-group="{{ $recordGroupKey }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $loadingTargetsWireTarget }}"
                        class="fi-ta-record-checkbox fi-checkbox-input"
                    />
                @endif
            </td>
        @endif

        @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::BeforeColumns && (! $isReordering))
            <td class="fi-ta-cell">
                <div
                    @class([
                        'fi-ta-actions',
                        match ($recordActionsAlignment) {
                            Alignment::Center => 'fi-align-center',
                            Alignment::Start, Alignment::Left => 'fi-align-start',
                            Alignment::Between, Alignment::Justify => 'fi-align-between',
                            Alignment::End, Alignment::Right => '',
                            default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                        },
                    ])
                >
                    @foreach ($recordActions as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </td>
        @endif

        @foreach ($columns as $column)
            @php
                $column->record($record);
                $column->rowLoop($loop->parent);
                $column->recordKey($recordKey);

                $columnAction = $column->getAction();
                $columnUrl = $column->getUrl();
                $columnHasStateBasedUrls = $column->hasStateBasedUrls();
                $isColumnClickDisabled = $column->isClickDisabled() || $isReordering;

                $columnWrapperTag = match (true) {
                    ($columnUrl || ($recordUrl && $columnAction === null)) && (! $columnHasStateBasedUrls) && (! $isColumnClickDisabled) => 'a',
                    ($columnAction || $recordAction) && (! $columnHasStateBasedUrls) && (! $isColumnClickDisabled) => 'button',
                    default => 'div',
                };

                if ($columnWrapperTag === 'button') {
                    if ($columnAction instanceof Action) {
                        $columnWireClickAction = "mountTableAction('{$columnAction->getName()}', '{$recordKey}')";
                    } elseif ($columnAction) {
                        $columnWireClickAction = "callTableColumnAction('{$column->getName()}', '{$recordKey}')";
                    } else {
                        if ($this->getTable()->getAction($recordAction)) {
                            $columnWireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                        } else {
                            $columnWireClickAction = "{$recordAction}('{$recordKey}')";
                        }
                    }
                }
            @endphp

            <td
                wire:key="{{ $this->getId() }}.table.record.{{ $recordKey }}.column.{{ $column->getName() }}"
                {!! $column->getCachedCellAttributeHtml() !!}
            >
                {!! $isStackedOnMobile ? '<div class="fi-ta-cell-label">' . e($column->getLabel()) . '</div><div class="fi-ta-cell-content">' : '' !!}
                <{{ $columnWrapperTag }}
                    @if ($columnWrapperTag === 'a')
                        {{ \Filament\Support\generate_href_html($columnUrl ?: $recordUrl, $columnUrl ? $column->shouldOpenUrlInNewTab() : $openRecordUrlInNewTab, hasNestedClickEventHandler: true) }}
                        @if (blank($columnUrl) && filled($recordUrl))
                            {{ $table->getExtraRecordLinkAttributeBag($record) }}
                        @endif
                    @elseif ($columnWrapperTag === 'button')
                        type="button"
                        wire:click.prevent.stop="{{ $columnWireClickAction }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $columnWireClickAction }}"
                    @endif
                    @class([
                        'fi-ta-col',
                        'fi-ta-col-has-column-url' => ($columnWrapperTag === 'a') && filled($columnUrl),
                    ])
                >
                    {{ $column }}
                </{{ $columnWrapperTag }}>
                {!! $isStackedOnMobile ? '</div>' : '' !!}
            </td>
        @endforeach

        @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::AfterColumns && (! $isReordering))
            <td class="fi-ta-cell">
                <div
                    @class([
                        'fi-ta-actions',
                        match ($recordActionsAlignment) {
                            Alignment::Center => 'fi-align-center',
                            Alignment::Start, Alignment::Left => 'fi-align-start',
                            Alignment::Between, Alignment::Justify => 'fi-align-between',
                            Alignment::End, Alignment::Right => '',
                            default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                        },
                    ])
                >
                    @foreach ($recordActions as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </td>
        @endif

        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells && (! $isReordering))
            <td class="fi-ta-cell fi-ta-selection-cell">
                @if ($recordIsSelectable)
                    <input
                        aria-label="{{ __('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey]) }}"
                        type="checkbox"
                        @if ($isSelectionDisabled)
                            disabled
                        @elseif ($maxSelectableRecords && ($maxSelectableRecords !== 1))
                            x-bind:disabled="! areRecordsToggleable([@js($recordKey)])"
                        @endif
                        value="{{ $recordKey }}"
                        x-on:click="toggleSelectedRecord(@js($recordKey))"
                        x-bind:checked="isRecordSelected(@js($recordKey)) ? 'checked' : null"
                        data-group="{{ $recordGroupKey }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $loadingTargetsWireTarget }}"
                        class="fi-ta-record-checkbox fi-checkbox-input"
                    />
                @endif
            </td>
        @endif

        @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::AfterCells && (! $isReordering))
            <td class="fi-ta-cell">
                <div
                    @class([
                        'fi-ta-actions',
                        match ($recordActionsAlignment) {
                            Alignment::Center => 'fi-align-center',
                            Alignment::Start, Alignment::Left => 'fi-align-start',
                            Alignment::Between, Alignment::Justify => 'fi-align-between',
                            Alignment::End, Alignment::Right => '',
                            default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                        },
                    ])
                >
                    @foreach ($recordActions as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </td>
        @endif
    </tr>
@endif
