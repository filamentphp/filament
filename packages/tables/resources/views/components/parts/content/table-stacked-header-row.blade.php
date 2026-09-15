@php
    use Illuminate\Support\Str;
@endphp

@if ($isStackedOnMobile && (count($sortableColumns) || ($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $selectsGroupsOnly))) && (! $isReordering))
    <tr class="fi-ta-table-stacked-header-row">
        <td colspan="100%" class="fi-ta-table-stacked-header-cell">
            @if (count($sortableColumns))
                <div
                    x-data="{
                        sort: $wire.$entangle('tableSort', true),
                        column: null,
                        direction: null,
                    }"
                    x-init="
                        if (sort) {
                            ;[column, direction] = sort.split(':')
                            direction ??= 'asc'
                        }

                        $watch('sort', function () {
                            if (! sort) {
                                return
                            }

                            ;[column, direction] = sort.split(':')
                            direction ??= 'asc'
                        })

                        $watch('direction', function () {
                            sort = column ? `${column}:${direction}` : null
                        })

                        $watch('column', function (newColumn, oldColumn) {
                            if (! newColumn) {
                                direction = null
                                sort = column ? `${column}:${direction}` : null

                                return
                            }

                            if (oldColumn) {
                                sort = column ? `${column}:${direction}` : null

                                return
                            }

                            direction = 'asc'
                            sort = column ? `${column}:${direction}` : null
                        })
                    "
                    class="fi-ta-table-stacked-sorting"
                >
                    <label>
                        <x-filament::input.wrapper
                            :prefix="__('filament-tables::table.sorting.fields.column.label')"
                        >
                            <x-filament::input.select x-model="column">
                                <option value="">
                                    {{ $defaultSortOptionLabel }}
                                </option>

                                @foreach ($sortableColumns as $sortableColumn)
                                    <option
                                        value="{{ $sortableColumn->getName() }}"
                                    >
                                        {{ $sortableColumn->getLabel() }}
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </label>

                    <label x-cloak x-show="column">
                        <span class="fi-sr-only">
                            {{ __('filament-tables::table.sorting.fields.direction.label') }}
                        </span>

                        <x-filament::input.wrapper>
                            <x-filament::input.select x-model="direction">
                                <option value="asc">
                                    {{ __('filament-tables::table.sorting.fields.direction.options.asc') }}
                                </option>

                                <option value="desc">
                                    {{ __('filament-tables::table.sorting.fields.direction.options.desc') }}
                                </option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </label>
                </div>
            @endif

            @if ($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $selectsGroupsOnly))
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
                    wire:key="{{ $this->getId() }}.table.bulk-select-page.checkbox.stacked.{{ Str::random() }}"
                    wire:loading.attr="disabled"
                    wire:target="{{ $loadingTargetsWireTarget }}"
                    class="fi-ta-page-checkbox fi-checkbox-input"
                />
            @endif
        </td>
    </tr>
@endif
