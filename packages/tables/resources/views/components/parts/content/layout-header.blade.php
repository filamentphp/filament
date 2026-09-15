@php
    use Filament\Tables\Columns\Column;
    use Illuminate\Support\Str;
@endphp

@if (! $isReordering)
    @php
        $sortableColumns = $hasSortingSettingsInContentHeader ? array_filter(
            $columns,
            fn (Column $column): bool => $column->isSortable(),
        ) : [];
    @endphp

    @if (($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $isReordering) && (! $selectsGroupsOnly)) || count($sortableColumns))
        <div class="fi-ta-content-header">
            @if ($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $isReordering) && (! $selectsGroupsOnly))
                <input
                    aria-label="{{ __('filament-tables::table.fields.bulk_select_page.label') }}"
                    type="checkbox"
                    @if ($isSelectionDisabled)
                        disabled
                    @elseif ($maxSelectableRecords)
                        x-bind:disabled="
                            const recordsOnPage = getRecordsOnPage()

                            return recordsOnPage.length && ! areRecordsToggleable(recordsOnPage)
                        "
                    @endif
                    x-bind:checked="
                        const recordsOnPage = getRecordsOnPage()

                        if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                            $el.checked = true
                            $el.indeterminate = false

                            return 'checked'
                        }

                        $el.checked = false
                        $el.indeterminate =
                            recordsOnPage.length && areRecordsPartiallySelected(recordsOnPage)

                        return null
                    "
                    x-on:click="toggleSelectRecordsOnPage"
                    {{-- Make sure the "checked" state gets re-evaluated after a Livewire request: --}}
                    wire:key="{{ $this->getId() }}.table.bulk-select-page.checkbox.{{ Str::random() }}"
                    wire:loading.attr="disabled"
                    wire:target="{{ $loadingTargetsWireTarget }}"
                    class="fi-ta-page-checkbox fi-checkbox-input"
                />
            @endif

            <?php if ($hasSortingSettingsInContentHeader) { ?>

            @include('filament-tables::components.parts.sorting-settings', ['table' => $table, 'part' => null])

            <?php } ?>
        </div>
    @endif
@endif
