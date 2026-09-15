@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\View\TablesIconAlias;
@endphp

<div
    @if ($hasCollapsibleColumnsLayout)
        x-data="{ isCollapsed: @js($collapsibleColumnsLayout->isCollapsed()) }"
        x-init="$dispatch('collapsible-table-row-initialized')"
        x-on:collapse-all-table-rows.window="isCollapsed = true"
        x-on:expand-all-table-rows.window="isCollapsed = false"
        x-bind:class="isCollapsed && 'fi-ta-record-collapsed'"
    @endif
    role="listitem"
    wire:key="{{ $this->getId() }}.table.records.{{ $recordKey }}"
    @if ($isReordering)
        x-sortable-item="{{ $recordKey }}"
        x-sortable-handle
    @endif
    @class([
        'fi-ta-record',
        'fi-clickable' => $recordUrl || $recordAction,
        'fi-ta-record-with-content-prefix' => $isReordering || $recordIsSelectable,
        'fi-ta-record-with-content-suffix' => $hasCollapsibleColumnsLayout && (! $isReordering),
        ...$table->getRecordClasses($record),
    ])
    x-bind:class="{
        {{ $group?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . \Illuminate\Support\Js::from($recordGroupTitle) . '),' : '' }}
        'fi-selected': @js($recordIsSelectable) && isRecordSelected(@js($recordKey)),
    }"
>
    @php
        $hasItemBeforeRecordContent = $isReordering || $recordIsSelectable;
        $hasItemAfterRecordContent = $hasCollapsibleColumnsLayout && (! $isReordering);
    @endphp

    @if ($isReordering)
        <button
            aria-label="{{ __('filament-tables::table.actions.reorder_record.label', ['key' => $recordKey]) }}"
            class="fi-ta-reorder-handle fi-icon-btn"
            type="button"
        >
            {{ \Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE) }}
        </button>
    @elseif ($recordIsSelectable)
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

    <div class="fi-ta-record-content-ctn">
        <div>
            @if ($recordUrl)
                <a
                    {{ \Filament\Support\generate_href_html($recordUrl, $openRecordUrlInNewTab, hasNestedClickEventHandler: true) }}
                    {{ $table->getExtraRecordLinkAttributeBag($record)->class(['fi-ta-record-content']) }}
                >
                    @foreach ($columnsLayout as $columnsLayoutComponent)
                        {{
                            $columnsLayoutComponent
                                ->record($record)
                                ->recordKey($recordKey)
                                ->rowLoop($loop)
                                ->renderInLayout()
                        }}
                    @endforeach
                </a>
            @elseif ($recordAction)
                @php
                    $recordWireClickAction = $table->getRecordAction($record)
                        ? "mountTableAction('{$recordAction}', '{$recordKey}')"
                        : $recordWireClickAction = "{$recordAction}('{$recordKey}')";
                @endphp

                <button
                    type="button"
                    wire:click="{{ $recordWireClickAction }}"
                    wire:loading.attr="disabled"
                    wire:target="{{ $recordWireClickAction }}"
                    class="fi-ta-record-content"
                >
                    @foreach ($columnsLayout as $columnsLayoutComponent)
                        {{
                            $columnsLayoutComponent
                                ->record($record)
                                ->recordKey($recordKey)
                                ->rowLoop($loop)
                                ->renderInLayout()
                        }}
                    @endforeach
                </button>
            @else
                <div class="fi-ta-record-content">
                    @foreach ($columnsLayout as $columnsLayoutComponent)
                        {{
                            $columnsLayoutComponent
                                ->record($record)
                                ->recordKey($recordKey)
                                ->rowLoop($loop)
                                ->renderInLayout()
                        }}
                    @endforeach
                </div>
            @endif

            @if ($hasCollapsibleColumnsLayout && (! $isReordering))
                <div
                    x-collapse
                    x-show="! isCollapsed"
                    class="fi-ta-record-content fi-collapsible"
                >
                    {{ $collapsibleColumnsLayout }}
                </div>
            @endif
        </div>

        @if ($recordActions && (! $isReordering))
            <div
                @class([
                    'fi-ta-actions fi-wrapped sm:fi-not-wrapped',
                    match ($recordActionsAlignment ?? Alignment::Start) {
                        Alignment::Start => 'fi-align-start',
                        Alignment::Center => 'fi-align-center',
                        Alignment::End => 'fi-align-end',
                    } => $contentGrid,
                    'fi-align-start md:fi-align-end' => ! $contentGrid,
                    'fi-ta-actions-before-columns-position' => $recordActionsPosition === RecordActionsPosition::BeforeColumns,
                ])
            >
                @foreach ($recordActions as $action)
                    {{ $action }}
                @endforeach
            </div>
        @endif
    </div>

    @if ($hasCollapsibleColumnsLayout && (! $isReordering))
        <button
            aria-label="{{ __('filament-tables::table.actions.toggle_record_content.label', ['key' => $recordKey]) }}"
            x-bind:aria-expanded="! isCollapsed"
            type="button"
            x-on:click="isCollapsed = ! isCollapsed"
            class="fi-ta-record-collapse-btn fi-icon-btn"
        >
            {{ \Filament\Support\generate_icon_html(Heroicon::ChevronDown, alias: TablesIconAlias::COLUMNS_COLLAPSE_BUTTON) }}
        </button>
    @endif
</div>
