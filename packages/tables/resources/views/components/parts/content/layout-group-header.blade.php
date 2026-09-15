@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\View\TablesIconAlias;
@endphp

@if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle)
    @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
        <table
            @class([
                'fi-ta-table',
                'fi-ta-table-reordering' => $isReordering,
            ])
        >
            <tbody>
                @php
                    $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                @endphp

                <x-filament-tables::summary.row
                    :columns="$columns"
                    extra-heading-column
                    :heading="
                        __('filament-tables::table.summary.subheadings.group', [
                            'group' => $previousRecordGroupTitle,
                            'label' => $pluralModelLabel,
                        ])
                    "
                    :placeholder-columns="false"
                    :query="$groupScopedAllTableSummaryQuery"
                    :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                />
            </tbody>
        </table>
    @endif

    <div
        @if ($isRecordGroupCollapsible = $group->isCollapsible())
            x-on:click="toggleCollapseGroup(@js($recordGroupTitle))"
            @if (! $hasSummary)
                x-bind:class="{ 'fi-collapsed': isGroupCollapsed(@js($recordGroupTitle)) }"
            @endif
        @endif
        @class([
            'fi-ta-group-header',
            'fi-collapsible' => $isRecordGroupCollapsible,
        ])
    >
        @if ($isSelectionEnabled && ($maxSelectableRecords !== 1))
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
@endif
