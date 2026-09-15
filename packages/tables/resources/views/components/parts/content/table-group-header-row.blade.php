@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Filament\Tables\View\TablesIconAlias;
@endphp

@if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
    @php
        $groupColumn = $group->getColumn();
        $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
    @endphp

    <x-filament-tables::summary.row
        :actions="$hasRecordActionsForAnyRecord"
        :actions-position="$recordActionsPosition"
        :columns="$columns"
        :group-column="$groupColumn"
        :groups-only="$isGroupsOnly"
        :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
        :query="$groupScopedAllTableSummaryQuery"
        :record-checkbox-position="$recordCheckboxPosition"
        :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
        :selection-enabled="$isSelectionEnabled"
    />
@endif

@if (! $isGroupsOnly)
    <tr class="fi-ta-row fi-ta-group-header-row">
        @php
            $isRecordGroupCollapsible = $group?->isCollapsible();
            $groupHeaderColspan = $columnsCount;

            if ($isSelectionEnabled) {
                $groupHeaderColspan--;

                if (
                    ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) &&
                    $hasRecordActionsForAnyRecord &&
                    ($recordActionsPosition === RecordActionsPosition::BeforeCells)
                ) {
                    $groupHeaderColspan--;
                }
            }
        @endphp

        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
            @if ($hasRecordActionsForAnyRecord && $recordActionsPosition === RecordActionsPosition::BeforeCells)
                <td></td>
            @endif

            <td class="fi-ta-cell fi-ta-group-selection-cell">
                @if ($maxSelectableRecords !== 1)
                    <input
                        aria-label="{{ __('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle]) }}"
                        type="checkbox"
                        data-group-selectable-record-keys="{{ json_encode($this->getGroupedSelectableTableRecordKeys($recordGroupKey)) }}"
                        @if ($isSelectionDisabled)
                            disabled
                        @else
                            x-on:click="toggleSelectRecords(JSON.parse($el.dataset.groupSelectableRecordKeys))"
                            @if ($maxSelectableRecords)
                                x-bind:disabled="
                                    const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                    return recordsInGroup.length && ! areRecordsToggleable(recordsInGroup)
                                "
                            @endif
                        @endif
                        x-bind:checked="
                            const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                            if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                $el.checked = true
                                $el.indeterminate = false

                                return 'checked'
                            }

                            $el.checked = false
                            $el.indeterminate =
                                recordsInGroup.length && areRecordsPartiallySelected(recordsInGroup)

                            return null
                        "
                        wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $loadingTargetsWireTarget }}"
                        class="fi-ta-group-checkbox fi-checkbox-input"
                    />
                @endif
            </td>
        @endif

        <td
            colspan="{{ $groupHeaderColspan }}"
            class="fi-ta-group-header-cell"
        >
            <div
                @if ($isRecordGroupCollapsible)
                    x-on:click="toggleCollapseGroup(@js($recordGroupTitle))"
                    x-bind:class="isGroupCollapsed(@js($recordGroupTitle)) ? 'fi-collapsed' : null"
                @endif
                @class([
                    'fi-ta-group-header',
                    'fi-collapsible' => $isRecordGroupCollapsible,
                ])
            >
                <div>
                    <{{ $secondLevelHeadingTag }} class="fi-ta-group-heading">
                        @if (filled($recordGroupLabel = ($group->isTitlePrefixedWithLabel() ? $group->getLabel() : null)))
                                {{ $recordGroupLabel }}:
                        @endif

                        {{ $recordGroupTitle }}
                    </{{ $secondLevelHeadingTag }}>

                    @if (filled($recordGroupDescription = $group->getDescription($record, $recordGroupTitle)))
                        <p class="fi-ta-group-description">
                            {{ $recordGroupDescription }}
                        </p>
                    @endif
                </div>

                @if ($isRecordGroupCollapsible)
                    <button
                        aria-label="{{ filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle }}"
                        x-bind:aria-expanded="! isGroupCollapsed(@js($recordGroupTitle))"
                        type="button"
                        class="fi-icon-btn fi-size-sm"
                    >
                        {{ \Filament\Support\generate_icon_html(Heroicon::ChevronUp, alias: TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: IconSize::Small) }}
                    </button>
                @endif
            </div>
        </td>

        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
            <td class="fi-ta-cell fi-ta-group-selection-cell">
                @if ($maxSelectableRecords !== 1)
                    <input
                        aria-label="{{ __('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle]) }}"
                        type="checkbox"
                        data-group-selectable-record-keys="{{ json_encode($this->getGroupedSelectableTableRecordKeys($recordGroupKey)) }}"
                        @if ($isSelectionDisabled)
                            disabled
                        @else
                            x-on:click="toggleSelectRecords(JSON.parse($el.dataset.groupSelectableRecordKeys))"
                            @if ($maxSelectableRecords)
                                x-bind:disabled="
                                    const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                    return recordsInGroup.length && ! areRecordsToggleable(recordsInGroup)
                                "
                            @endif
                        @endif
                        x-bind:checked="
                            const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                            if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                $el.checked = true
                                $el.indeterminate = false

                                return 'checked'
                            }

                            $el.checked = false
                            $el.indeterminate =
                                recordsInGroup.length && areRecordsPartiallySelected(recordsInGroup)

                            return null
                        "
                        wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $loadingTargetsWireTarget }}"
                        class="fi-ta-group-checkbox fi-checkbox-input"
                    />
                @endif
            </td>
        @endif
    </tr>
@endif
