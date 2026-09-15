@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\View\TablesRenderHook;
    use Illuminate\Support\Number;

    $isReordering = $table->isReordering();
@endphp

@if ($isReordering)
    <div
        x-cloak
        role="status"
        aria-live="polite"
        wire:key="{{ $this->getId() }}.table.reorder.indicator"
        class="fi-ta-reorder-indicator"
    >
        {{
            \Filament\Support\generate_loading_indicator_html(new Filament\Support\View\ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => 'reorderTable',
            ]))
        }}

        {{ __('filament-tables::table.reorder_indicator') }}
    </div>
@elseif ($table->canSelectRecords() && ($table->getMaxSelectableRecords() !== 1) && $table->isLoaded())
    @php
        $allSelectableRecordsCount = $table->getAllSelectableRecordsCount();
        $page = $this->getTablePage();
    @endphp

    <div
        x-cloak
        role="status"
        aria-live="polite"
        aria-atomic="true"
        x-bind:hidden="! getSelectedRecordsCount()"
        x-show="getSelectedRecordsCount()"
        wire:key="{{ $this->getId() }}.table.selection.indicator"
        class="fi-ta-selection-indicator"
    >
        <div>
            {{
                \Filament\Support\generate_loading_indicator_html(new Filament\Support\View\ComponentAttributeBag([
                    'x-show' => 'isLoading',
                ]))
            }}

            <span
                x-text="
                    window.pluralize(@js(__('filament-tables::table.selection_indicator.selected_count')), getSelectedRecordsCount(), {
                        count: new Intl.NumberFormat(@js(str_replace('_', '-', app()->getLocale()))).format(getSelectedRecordsCount()),
                    })
                "
            ></span>
        </div>

        @if (! $table->isSelectionDisabled())
            <div>
                {{ FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE, scopes: static::class) }}

                <div class="fi-ta-selection-indicator-actions-ctn">
                    @if (! $table->selectsGroupsOnly())
                        <x-filament::link
                            color="primary"
                            tag="button"
                            x-on:click="selectAllRecords"
                            x-show="canSelectAllRecords()"
                            {{-- Make sure the Alpine attributes get re-evaluated after a Livewire request: --}}
                            :wire:key="$this->getId() . 'table.selection.indicator.actions.select-all.' . $allSelectableRecordsCount . '.' . $page"
                        >
                            {{ trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $allSelectableRecordsCount, ['count' => Number::format($allSelectableRecordsCount, locale: app()->getLocale())]) }}
                        </x-filament::link>
                    @endif

                    <x-filament::link
                        color="danger"
                        tag="button"
                        x-on:click="deselectAllRecords"
                    >
                        {{ __('filament-tables::table.selection_indicator.actions.deselect_all.label') }}
                    </x-filament::link>
                </div>

                {{ FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_AFTER, scopes: static::class) }}
            </div>
        @endif
    </div>
@endif
