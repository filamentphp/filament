@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Actions\HeaderActionsPosition;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\ColumnGroup;
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Illuminate\Support\Str;
    use Illuminate\View\ComponentAttributeBag;

    $actions = $getActions();
    $flatActionsCount = count($getFlatActions());
    $actionsAlignment = $getActionsAlignment();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();

    if (! $actionsAlignment instanceof Alignment) {
        $actionsAlignment = filled($actionsAlignment) ? (Alignment::tryFrom($actionsAlignment) ?? $actionsAlignment) : null;
    }

    $activeFiltersCount = $getActiveFiltersCount();
    $columns = $getVisibleColumns();
    $collapsibleColumnsLayout = $getCollapsibleColumnsLayout();
    $columnsLayout = $getColumnsLayout();
    $content = $getContent();
    $contentGrid = $getContentGrid();
    $contentFooter = $getContentFooter();
    $filterIndicators = $getFilterIndicators();
    $filtersApplyAction = $getFiltersApplyAction();
    $filtersForm = $getFiltersForm();
    $filtersFormWidth = $getFiltersFormWidth();
    $hasColumnGroups = $hasColumnGroups();
    $hasColumnsLayout = $hasColumnsLayout();
    $hasSummary = $hasSummary();
    $header = $getHeader();
    $headerActions = array_filter(
        $getHeaderActions(),
        fn (\Filament\Actions\Action | \Filament\Actions\BulkAction | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $bulkActions = array_filter(
        $getBulkActions(),
        fn (\Filament\Actions\BulkAction | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $groups = $getGroups();
    $description = $getDescription();
    $isGroupsOnly = $isGroupsOnly() && $group;
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $areGroupingSettingsVisible = (! $isReordering) && count($groups) && (! $areGroupingSettingsHidden());
    $isGroupingDirectionSettingHidden = $isGroupingDirectionSettingHidden();
    $areGroupingSettingsInDropdownOnDesktop = $areGroupingSettingsInDropdownOnDesktop();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSearchOnBlur = $isSearchOnBlur();
    $isSelectionEnabled = $isSelectionEnabled() && (! $isGroupsOnly);
    $selectsCurrentPageOnly = $selectsCurrentPageOnly();
    $recordCheckboxPosition = $getRecordCheckboxPosition();
    $isStriped = $isStriped();
    $isLoaded = $isLoaded();
    $hasFilters = $isFilterable();
    $filtersLayout = $getFiltersLayout();
    $filtersTriggerAction = $getFiltersTriggerAction();
    $hasFiltersDialog = $hasFilters && in_array($filtersLayout, [FiltersLayout::Dropdown, FiltersLayout::Modal]);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersAboveContentCollapsible = $hasFilters && ($filtersLayout === FiltersLayout::AboveContentCollapsible);
    $hasFiltersBelowContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $hasColumnToggleDropdown = $hasToggleableColumns();
    $hasHeader = $header || $heading || $description || ($headerActions && (! $isReordering)) || $isReorderable || $areGroupingSettingsVisible || $isGlobalSearchVisible || $hasFilters || count($filterIndicators) || $hasColumnToggleDropdown;
    $hasHeaderToolbar = $isReorderable || $areGroupingSettingsVisible || $isGlobalSearchVisible || $hasFiltersDialog || $hasColumnToggleDropdown;
    $headingTag = $getHeadingTag();
    $secondLevelHeadingTag = $heading ? $getHeadingTag(1) : $headingTag;
    $pluralModelLabel = $getPluralModelLabel();
    $records = $isLoaded ? $getRecords() : null;
    $searchDebounce = $getSearchDebounce();
    $allSelectableRecordsCount = ($isSelectionEnabled && $isLoaded) ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);
    $reorderRecordsTriggerAction = $getReorderRecordsTriggerAction($isReordering);
    $toggleColumnsTriggerAction = $getToggleColumnsTriggerAction();
    $page = $this->getTablePage();
    $defaultSortOptionLabel = $getDefaultSortOptionLabel();
    $sortDirection = $getSortDirection();

    if (count($actions) && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
    }

    if ($group) {
        $groupedSummarySelectedState = $this->getTableSummarySelectedState($this->getAllTableSummaryQuery(), modifyQueryUsing: fn (\Illuminate\Database\Query\Builder $query) => $group->groupQuery($query, model: $getQuery()->getModel()));
    }
@endphp

<div
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
    @if (FilamentView::hasSpaMode())
        x-load="visible"
    @else
        x-load
    @endif
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('table', 'filament/tables') }}"
    x-data="table"
    @class([
        'fi-ta',
        'fi-loading' => $records === null,
    ])
>
    <div
        @class([
            'fi-ta-ctn',
            'fi-ta-ctn-with-header' => $hasHeader,
        ])
    >
        <div
            @if (! $hasHeader) x-cloak @endif
            x-show="@js($hasHeader) || (selectedRecords.length && @js(count($bulkActions)))"
            class="fi-ta-header-ctn"
        >
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::HEADER_BEFORE, scopes: static::class) }}

            @if ($header)
                {{ $header }}
            @elseif (($heading || $description || $headerActions) && ! $isReordering)
                <div
                    @class([
                        'fi-ta-header',
                        'fi-ta-header-adaptive-actions-position' => $headerActions && ($headerActionsPosition === HeaderActionsPosition::Adaptive),
                    ])
                >
                    @if ($heading || $description)
                        <div>
                            @if ($heading)
                                <{{ $headingTag }}
                                    class="fi-ta-header-heading"
                                >
                                    {{ $heading }}
                                </{{ $headingTag }}>
                            @endif

                            @if ($description)
                                <p class="fi-ta-header-description">
                                    {{ $description }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ((! $isReordering) && $headerActions)
                        <div class="fi-ta-actions fi-align-start fi-wrapped">
                            @foreach ($headerActions as $action)
                                {{ $action }}
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::HEADER_AFTER, scopes: static::class) }}

            @if ($hasFiltersAboveContent)
                <div
                    x-data="{ areFiltersOpen: @js(! $hasFiltersAboveContentCollapsible) }"
                    x-bind:class="{ 'fi-open': areFiltersOpen }"
                    @class([
                        'fi-ta-filters-above-content-ctn',
                    ])
                >
                    <x-filament-tables::filters
                        :apply-action="$filtersApplyAction"
                        :form="$filtersForm"
                        :heading-tag="$secondLevelHeadingTag"
                        x-cloak
                        x-show="areFiltersOpen"
                    />

                    @if ($hasFiltersAboveContentCollapsible)
                        <span
                            x-on:click="areFiltersOpen = ! areFiltersOpen"
                            class="fi-ta-filters-trigger-action-ctn"
                        >
                            {{ $filtersTriggerAction->badge($activeFiltersCount) }}
                        </span>
                    @endif
                </div>
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_BEFORE, scopes: static::class) }}

            <div
                @if (! $hasHeaderToolbar) x-cloak @endif
                x-show="@js($hasHeaderToolbar) || (selectedRecords.length && @js(count($bulkActions)))"
                class="fi-ta-header-toolbar"
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_START, scopes: static::class) }}

                <div>
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_REORDER_TRIGGER_BEFORE, scopes: static::class) }}

                    @if ($isReorderable)
                        <span x-show="! selectedRecords.length">
                            {{ $reorderRecordsTriggerAction }}
                        </span>
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, scopes: static::class) }}

                    @if ((! $isReordering) && count($bulkActions))
                        <div
                            x-cloak
                            x-show="selectedRecords.length"
                            class="fi-ta-actions"
                        >
                            @foreach ($bulkActions as $action)
                                {{ $action }}
                            @endforeach
                        </div>
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE, scopes: static::class) }}

                    @if ($areGroupingSettingsVisible)
                        <div
                            x-data="{
                                direction: $wire.$entangle('tableGroupingDirection', true),
                                group: $wire.$entangle('tableGrouping', true),
                            }"
                            x-init="
                                $watch('group', function (newGroup, oldGroup) {
                                    if (newGroup && direction) {
                                        return
                                    }

                                    if (! newGroup) {
                                        direction = null

                                        return
                                    }

                                    if (oldGroup) {
                                        return
                                    }

                                    direction = 'asc'
                                })
                            "
                            class="fi-ta-grouping-settings"
                        >
                            <x-filament::dropdown
                                placement="bottom-start"
                                shift
                                width="xs"
                                wire:key="{{ $this->getId() }}.table.grouping"
                                @class([
                                    'sm:fi-hidden' => ! $areGroupingSettingsInDropdownOnDesktop,
                                ])
                            >
                                <x-slot name="trigger">
                                    {{ $getGroupRecordsTriggerAction() }}
                                </x-slot>

                                <div class="fi-ta-grouping-settings-fields">
                                    <label>
                                        <span>
                                            {{ __('filament-tables::table.grouping.fields.group.label') }}
                                        </span>

                                        <x-filament::input.wrapper>
                                            <x-filament::input.select
                                                x-model="group"
                                                x-on:change="resetCollapsedGroups()"
                                            >
                                                <option value="">-</option>

                                                @foreach ($groups as $groupOption)
                                                    <option
                                                        value="{{ $groupOption->getId() }}"
                                                    >
                                                        {{ $groupOption->getLabel() }}
                                                    </option>
                                                @endforeach
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </label>

                                    @if (! $isGroupingDirectionSettingHidden)
                                        <label x-cloak x-show="group">
                                            <span>
                                                {{ __('filament-tables::table.grouping.fields.direction.label') }}
                                            </span>

                                            <x-filament::input.wrapper>
                                                <x-filament::input.select
                                                    x-model="direction"
                                                >
                                                    <option value="asc">
                                                        {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                                                    </option>

                                                    <option value="desc">
                                                        {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                                                    </option>
                                                </x-filament::input.select>
                                            </x-filament::input.wrapper>
                                        </label>
                                    @endif
                                </div>
                            </x-filament::dropdown>

                            @if (! $areGroupingSettingsInDropdownOnDesktop)
                                <div class="fi-ta-grouping-settings-fields">
                                    <label>
                                        <span class="fi-sr-only">
                                            {{ __('filament-tables::table.grouping.fields.group.label') }}
                                        </span>

                                        <x-filament::input.wrapper>
                                            <x-filament::input.select
                                                x-model="group"
                                                x-on:change="resetCollapsedGroups()"
                                            >
                                                <option value="">
                                                    {{ __('filament-tables::table.grouping.fields.group.placeholder') }}
                                                </option>

                                                @foreach ($groups as $groupOption)
                                                    <option
                                                        value="{{ $groupOption->getId() }}"
                                                    >
                                                        {{ $groupOption->getLabel() }}
                                                    </option>
                                                @endforeach
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </label>

                                    @if (! $isGroupingDirectionSettingHidden)
                                        <label x-cloak x-show="group">
                                            <span class="fi-sr-only">
                                                {{ __('filament-tables::table.grouping.fields.direction.label') }}
                                            </span>

                                            <x-filament::input.wrapper>
                                                <x-filament::input.select
                                                    x-model="direction"
                                                >
                                                    <option value="asc">
                                                        {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                                                    </option>

                                                    <option value="desc">
                                                        {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                                                    </option>
                                                </x-filament::input.select>
                                            </x-filament::input.wrapper>
                                        </label>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER, scopes: static::class) }}
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersDialog || $hasColumnToggleDropdown)
                    <div>
                        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_SEARCH_BEFORE, scopes: static::class) }}

                        @if ($isGlobalSearchVisible)
                            @php
                                $searchPlaceholder = $getSearchPlaceholder();
                            @endphp

                            <x-filament-tables::search-field
                                :debounce="$searchDebounce"
                                :on-blur="$isSearchOnBlur"
                                :placeholder="$searchPlaceholder"
                            />
                        @endif

                        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_SEARCH_AFTER, scopes: static::class) }}

                        @if ($hasFiltersDialog || $hasColumnToggleDropdown)
                            @if ($hasFiltersDialog)
                                @if (($filtersLayout === FiltersLayout::Modal) || $filtersTriggerAction->isModalSlideOver())
                                    @php
                                        $filtersTriggerActionModalAlignment = $filtersTriggerAction->getModalAlignment();
                                        $filtersTriggerActionIsModalAutofocused = $filtersTriggerAction->isModalAutofocused();
                                        $filtersTriggerActionHasModalCloseButton = $filtersTriggerAction->hasModalCloseButton();
                                        $filtersTriggerActionIsModalClosedByClickingAway = $filtersTriggerAction->isModalClosedByClickingAway();
                                        $filtersTriggerActionIsModalClosedByEscaping = $filtersTriggerAction->isModalClosedByEscaping();
                                        $filtersTriggerActionModalDescription = $filtersTriggerAction->getModalDescription();
                                        $filtersTriggerActionVisibleModalFooterActions = $filtersTriggerAction->getVisibleModalFooterActions();
                                        $filtersTriggerActionModalFooterActionsAlignment = $filtersTriggerAction->getModalFooterActionsAlignment();
                                        $filtersTriggerActionModalHeading = $filtersTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.filters.heading');
                                        $filtersTriggerActionModalIcon = $filtersTriggerAction->getModalIcon();
                                        $filtersTriggerActionModalIconColor = $filtersTriggerAction->getModalIconColor();
                                        $filtersTriggerActionIsModalSlideOver = $filtersTriggerAction->isModalSlideOver();
                                        $filtersTriggerActionIsModalFooterSticky = $filtersTriggerAction->isModalFooterSticky();
                                        $filtersTriggerActionIsModalHeaderSticky = $filtersTriggerAction->isModalHeaderSticky();
                                    @endphp

                                    <x-filament::modal
                                        :alignment="$filtersTriggerActionModalAlignment"
                                        :autofocus="$filtersTriggerActionIsModalAutofocused"
                                        :close-button="$filtersTriggerActionHasModalCloseButton"
                                        :close-by-clicking-away="$filtersTriggerActionIsModalClosedByClickingAway"
                                        :close-by-escaping="$filtersTriggerActionIsModalClosedByEscaping"
                                        :description="$filtersTriggerActionModalDescription"
                                        :footer-actions="$filtersTriggerActionVisibleModalFooterActions"
                                        :footer-actions-alignment="$filtersTriggerActionModalFooterActionsAlignment"
                                        :heading="$filtersTriggerActionModalHeading"
                                        :icon="$filtersTriggerActionModalIcon"
                                        :icon-color="$filtersTriggerActionModalIconColor"
                                        :slide-over="$filtersTriggerActionIsModalSlideOver"
                                        :sticky-footer="$filtersTriggerActionIsModalFooterSticky"
                                        :sticky-header="$filtersTriggerActionIsModalHeaderSticky"
                                        :width="$filtersFormWidth"
                                        :wire:key="$this->getId() . '.table.filters'"
                                        class="fi-ta-filters-modal"
                                    >
                                        <x-slot name="trigger">
                                            {{ $filtersTriggerAction->badge($activeFiltersCount) }}
                                        </x-slot>

                                        {{ $filtersTriggerAction->getModalContent() }}

                                        {{ $filtersForm }}

                                        {{ $filtersTriggerAction->getModalContentFooter() }}
                                    </x-filament::modal>
                                @else
                                    @php
                                        $filtersFormMaxHeight = $getFiltersFormMaxHeight();
                                    @endphp

                                    <x-filament::dropdown
                                        :max-height="$filtersFormMaxHeight"
                                        placement="bottom-end"
                                        shift
                                        :width="$filtersFormWidth"
                                        :wire:key="$this->getId() . '.table.filters'"
                                        class="fi-ta-filters-dropdown"
                                    >
                                        <x-slot name="trigger">
                                            {{ $filtersTriggerAction->badge($activeFiltersCount) }}
                                        </x-slot>

                                        <x-filament-tables::filters
                                            :apply-action="$filtersApplyAction"
                                            :form="$filtersForm"
                                            :heading-tag="$secondLevelHeadingTag"
                                        />
                                    </x-filament::dropdown>
                                @endif
                            @endif

                            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE, scopes: static::class) }}

                            @if ($hasColumnToggleDropdown)
                                @php
                                    $columnToggleFormMaxHeight = $getColumnToggleFormMaxHeight();
                                    $columnToggleFormWidth = $getColumnToggleFormWidth();
                                @endphp

                                <x-filament::dropdown
                                    :max-height="$columnToggleFormMaxHeight"
                                    placement="bottom-end"
                                    shift
                                    :width="$columnToggleFormWidth"
                                    :wire:key="$this->getId() . '.table.column-toggle'"
                                    class="fi-ta-col-toggle"
                                >
                                    <x-slot name="trigger">
                                        {{ $toggleColumnsTriggerAction }}
                                    </x-slot>

                                    <div class="fi-ta-col-toggle-form-ctn">
                                        <{{ $secondLevelHeadingTag }}
                                            class="fi-ta-col-toggle-heading"
                                        >
                                            {{ __('filament-tables::table.column_toggle.heading') }}
                                        </{{ $secondLevelHeadingTag }}>

                                        {{ $getColumnToggleForm() }}
                                    </div>
                                </x-filament::dropdown>
                            @endif

                            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER, scopes: static::class) }}
                        @endif
                    </div>
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_END) }}
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::TOOLBAR_AFTER) }}
        </div>

        @if ($isReordering)
            <div
                x-cloak
                wire:key="{{ $this->getId() }}.table.reorder.indicator"
                class="fi-ta-reorder-indicator"
            >
                {{
                    \Filament\Support\generate_loading_indicator_html(new \Illuminate\View\ComponentAttributeBag([
                        'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                        'wire:target' => 'reorderTable',
                    ]))
                }}

                {{ __('filament-tables::table.reorder_indicator') }}
            </div>
        @elseif ($isSelectionEnabled && $isLoaded)
            <div
                x-cloak
                x-bind:hidden="! selectedRecords.length"
                x-show="selectedRecords.length"
                wire:key="{{ $this->getId() }}.table.selection.indicator"
                class="fi-ta-selection-indicator"
            >
                <div>
                    {{
                        \Filament\Support\generate_loading_indicator_html(new \Illuminate\View\ComponentAttributeBag([
                            'x-show' => 'isLoading',
                        ]))
                    }}

                    <span
                        x-text="
                            window.pluralize(@js(__('filament-tables::table.selection_indicator.selected_count')), selectedRecords.length, {
                                count: selectedRecords.length,
                            })
                        "
                    ></span>
                </div>

                <div>
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE, scopes: static::class) }}

                    <div class="fi-ta-selection-indicator-actions-ctn">
                        <x-filament::link
                            color="primary"
                            tag="button"
                            x-on:click="selectAllRecords"
                            :x-show="$selectsCurrentPageOnly ? '! areRecordsSelected(getRecordsOnPage())' : $allSelectableRecordsCount . ' !== selectedRecords.length'"
                            {{-- Make sure the Alpine attributes get re-evaluated after a Livewire request: --}}
                            :wire:key="$this->getId() . 'table.selection.indicator.actions.select-all.' . $allSelectableRecordsCount . '.' . $page"
                        >
                            {{ trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $allSelectableRecordsCount, ['count' => \Illuminate\Support\Number::format($allSelectableRecordsCount)]) }}
                        </x-filament::link>

                        <x-filament::link
                            color="danger"
                            tag="button"
                            x-on:click="deselectAllRecords"
                        >
                            {{ __('filament-tables::table.selection_indicator.actions.deselect_all.label') }}
                        </x-filament::link>
                    </div>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Tables\View\TablesRenderHook::SELECTION_INDICATOR_ACTIONS_AFTER, scopes: static::class) }}
                </div>
            </div>
        @endif

        @if ($filterIndicators)
            <div class="fi-ta-filter-indicators">
                <div>
                    <span class="fi-ta-filter-indicators-label">
                        {{ __('filament-tables::table.filters.indicator') }}
                    </span>

                    <div class="fi-ta-filter-indicators-badges-ctn">
                        @foreach ($filterIndicators as $indicator)
                            @php
                                $indicatorColor = $indicator->getColor();
                            @endphp

                            <x-filament::badge :color="$indicatorColor">
                                {{ $indicator->getLabel() }}

                                @if ($indicator->isRemovable())
                                    @php
                                        $indicatorRemoveLivewireClickHandler = $indicator->getRemoveLivewireClickHandler();
                                    @endphp

                                    <x-slot
                                        name="deleteButton"
                                        :label="__('filament-tables::table.filters.actions.remove.label')"
                                        :wire:click="$indicatorRemoveLivewireClickHandler"
                                        wire:loading.attr="disabled"
                                        wire:target="removeTableFilter"
                                    ></x-slot>
                                @endif
                            </x-filament::badge>
                        @endforeach
                    </div>
                </div>

                @if (collect($filterIndicators)->contains(fn (\Filament\Tables\Filters\Indicator $indicator): bool => $indicator->isRemovable()))
                    <button
                        type="button"
                        x-tooltip="{
                            content: @js(__('filament-tables::table.filters.actions.remove_all.tooltip')),
                            theme: $store.theme,
                        }"
                        wire:click="removeTableFilters"
                        wire:loading.attr="disabled"
                        wire:target="removeTableFilters,removeTableFilter"
                        class="fi-icon-btn fi-size-sm"
                    >
                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::XMark, alias: 'tables::filters.remove-all-button', size: \Filament\Support\Enums\IconSize::Small) }}
                    </button>
                @endif
            </div>
        @endif

        @if (((! $content) && (! $hasColumnsLayout)) || ($records === null) || count($records))
            <div
                @if ((! $isReordering) && ($pollingInterval = $getPollingInterval()))
                    wire:poll.{{ $pollingInterval }}
                @endif
                class="fi-ta-content-ctn"
            >
                @if (($content || $hasColumnsLayout) && ($records !== null) && count($records))
                    @if (! $isReordering)
                        @php
                            $sortableColumns = array_filter(
                                $columns,
                                fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                            );
                        @endphp

                        @if ($isSelectionEnabled || count($sortableColumns))
                            <div class="fi-ta-content-header">
                                @if ($isSelectionEnabled && (! $isReordering))
                                    <input
                                        aria-label="{{ __('filament-tables::table.fields.bulk_select_page.label') }}"
                                        type="checkbox"
                                        x-bind:checked="
                                            const recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                        x-on:click="toggleSelectRecordsOnPage"
                                        {{-- Make sure the "checked" state gets re-evaluated after a Livewire request: --}}
                                        wire:key="{{ $this->getId() }}.table.bulk-select-page.checkbox.{{ \Illuminate\Support\Str::random() }}"
                                        wire:loading.attr="disabled"
                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                        class="fi-ta-page-checkbox fi-checkbox-input"
                                    />
                                @endif

                                @if (count($sortableColumns))
                                    <div
                                        x-data="{
                                            column: $wire.$entangle('tableSortColumn', true),
                                            direction: $wire.$entangle('tableSortDirection', true),
                                        }"
                                        x-init="
                                            $watch('column', function (newColumn, oldColumn) {
                                                if (! newColumn) {
                                                    direction = null

                                                    return
                                                }

                                                if (oldColumn) {
                                                    return
                                                }

                                                direction = 'asc'
                                            })
                                        "
                                        class="fi-ta-sorting-settings"
                                    >
                                        <label>
                                            <x-filament::input.wrapper
                                                :prefix="__('filament-tables::table.sorting.fields.column.label')"
                                            >
                                                <x-filament::input.select
                                                    x-model="column"
                                                >
                                                    <option value="">
                                                        {{ $defaultSortOptionLabel }}
                                                    </option>

                                                    @foreach ($sortableColumns as $column)
                                                        <option
                                                            value="{{ $column->getName() }}"
                                                        >
                                                            {{ $column->getLabel() }}
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
                                                <x-filament::input.select
                                                    x-model="direction"
                                                >
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
                            </div>
                        @endif
                    @endif

                    @if ($content)
                        {{ $content->with(['records' => $records]) }}
                    @else
                        <div
                            @if ($isReorderable)
                                x-on:end.stop="
                                    $wire.reorderTable(
                                        $event.target.sortable.toArray(),
                                        $event.item.getAttribute('x-sortable-item'),
                                    )
                                "
                                x-sortable
                                data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                            @endif
                            {{
                                (new ComponentAttributeBag)
                                    ->when($contentGrid, fn (ComponentAttributeBag $attributes) => $attributes->grid($contentGrid))
                                    ->class([
                                        'fi-ta-content',
                                        'fi-ta-content-grid' => $contentGrid,
                                        'fi-ta-content-grouped' => $this->getTableGrouping(),
                                    ])
                            }}
                        >
                            @php
                                $previousRecord = null;
                                $previousRecordGroupKey = null;
                                $previousRecordGroupTitle = null;
                            @endphp

                            @foreach ($records as $record)
                                @php
                                    $recordAction = $getRecordAction($record);
                                    $recordKey = $getRecordKey($record);
                                    $recordUrl = $getRecordUrl($record);
                                    $openRecordUrlInNewTab = $shouldOpenRecordUrlInNewTab($record);
                                    $recordGroupKey = $group?->getStringKey($record);
                                    $recordGroupTitle = $group?->getTitle($record);
                                    $isRecordGroupCollapsible = $group?->isCollapsible();

                                    $collapsibleColumnsLayout?->record($record)->recordKey($recordKey);
                                    $hasCollapsibleColumnsLayout = (bool) $collapsibleColumnsLayout?->isVisible();

                                    $recordActions = array_reduce(
                                        $actions,
                                        function (array $carry, $action) use ($record): array {
                                            if (! $action instanceof \Filament\Actions\ActionGroup) {
                                                $action = clone $action;
                                            }

                                            if (! $action instanceof \Filament\Actions\BulkAction) {
                                                $action->record($record);
                                            }

                                            if ($action->isHidden()) {
                                                return $carry;
                                            }

                                            $carry[] = $action;

                                            return $carry;
                                        },
                                        initial: [],
                                    );
                                @endphp

                                @if ($recordGroupTitle !== $previousRecordGroupTitle)
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
                                        @if ($isSelectionEnabled)
                                            <input
                                                aria-label="{{ __('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle]) }}"
                                                type="checkbox"
                                                x-bind:checked="
                                                    const recordsInGroup = getRecordsInGroupOnPage(@js($recordGroupKey))

                                                    if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                                        $el.checked = true

                                                        return 'checked'
                                                    }

                                                    $el.checked = false

                                                    return null
                                                "
                                                x-on:click="toggleSelectRecordsInGroup(@js($recordGroupKey))"
                                                wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                                                wire:loading.attr="disabled"
                                                wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                class="fi-ta-record-checkbox fi-ta-group-checkbox fi-checkbox-input"
                                            />
                                        @endif

                                        <div>
                                            <{{ $secondLevelHeadingTag }}
                                                class="fi-ta-group-heading"
                                            >
                                                @if (filled($recordGroupLabel = ($group->isTitlePrefixedWithLabel() ? $group->getLabel() : null)))
                                                        {{ $recordGroupLabel }}:
                                                @endif

                                                {{ $recordGroupTitle }}
                                            </{{ $secondLevelHeadingTag }}>

                                            @if (filled($recordGroupDescription = $group->getDescription($record, $recordGroupTitle)))
                                                <p
                                                    class="fi-ta-group-description"
                                                >
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
                                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, alias: 'tables::grouping.collapse-button', size: \Filament\Support\Enums\IconSize::Small) }}
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                <div
                                    @if ($hasCollapsibleColumnsLayout)
                                        x-data="{ isCollapsed: @js($collapsibleColumnsLayout->isCollapsed()) }"
                                        x-init="$dispatch('collapsible-table-row-initialized')"
                                        x-on:collapse-all-table-rows.window="isCollapsed = true"
                                        x-on:expand-all-table-rows.window="isCollapsed = false"
                                        x-bind:class="isCollapsed && 'fi-ta-record-collapsed'"
                                    @endif
                                    wire:key="{{ $this->getId() }}.table.records.{{ $recordKey }}"
                                    @if ($isReordering)
                                        x-sortable-item="{{ $recordKey }}"
                                        x-sortable-handle
                                    @endif
                                    @class([
                                        'fi-ta-record',
                                        'fi-clickable' => $recordUrl || $recordAction,
                                        'fi-ta-record-with-content-prefix' => $isReordering || ($isSelectionEnabled && $isRecordSelectable($record)),
                                        'fi-ta-record-with-content-suffix' => $hasCollapsibleColumnsLayout && (! $isReordering),
                                        ...$getRecordClasses($record),
                                    ])
                                    x-bind:class="{
                                        {{ $group?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . \Illuminate\Support\Js::from($recordGroupTitle) . '),' : '' }}
                                        'fi-selected': isRecordSelected(@js($recordKey)),
                                    }"
                                >
                                    @php
                                        $hasItemBeforeRecordContent = $isReordering || ($isSelectionEnabled && $isRecordSelectable($record));
                                        $hasItemAfterRecordContent = $hasCollapsibleColumnsLayout && (! $isReordering);
                                    @endphp

                                    @if ($isReordering)
                                        <button
                                            class="fi-ta-reorder-handle fi-icon-btn"
                                            type="button"
                                        >
                                            {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: 'tables::reorder.handle') }}
                                        </button>
                                    @elseif ($isSelectionEnabled && $isRecordSelectable($record))
                                        <input
                                            aria-label="{{ __('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey]) }}"
                                            type="checkbox"
                                            value="{{ $recordKey }}"
                                            x-model="selectedRecords"
                                            data-group="{{ $recordGroupKey }}"
                                            wire:loading.attr="disabled"
                                            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                            class="fi-ta-record-checkbox fi-checkbox-input"
                                        />
                                    @endif

                                    <div class="fi-ta-record-content-ctn">
                                        <div>
                                            @if ($recordUrl)
                                                <a
                                                    {{ \Filament\Support\generate_href_html($recordUrl, $openRecordUrlInNewTab) }}
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
                                                </a>
                                            @elseif ($recordAction)
                                                @php
                                                    $recordWireClickAction = $getAction($recordAction)
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
                                                <div
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
                                                    'fi-align-start' => $contentGrid,
                                                    'md:fi-align-end' => ! $contentGrid,
                                                    'fi-ta-actions-before-columns-position' => $actionsPosition === ActionsPosition::BeforeColumns,
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
                                            type="button"
                                            x-on:click="isCollapsed = ! isCollapsed"
                                            class="fi-ta-record-collapse-btn fi-icon-btn"
                                        >
                                            {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronDown, alias: 'tables::columns.collapse-button') }}
                                        </button>
                                    @endif
                                </div>

                                @php
                                    $previousRecordGroupKey = $recordGroupKey;
                                    $previousRecordGroupTitle = $recordGroupTitle;
                                    $previousRecord = $record;
                                @endphp
                            @endforeach

                            @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                                <table class="fi-ta-table">
                                    <tbody>
                                        @php
                                            $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                        @endphp

                                        <x-filament-tables::summary.row
                                            :columns="$columns"
                                            extra-heading-column
                                            :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                            :placeholder-columns="false"
                                            :query="$groupScopedAllTableSummaryQuery"
                                            :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                        />
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif

                    @if (($content || $hasColumnsLayout) && $contentFooter)
                        {{
                            $contentFooter->with([
                                'columns' => $columns,
                                'records' => $records,
                            ])
                        }}
                    @endif

                    @if ($hasSummary && (! $isReordering))
                        <table class="fi-ta-table">
                            <tbody>
                                <x-filament-tables::summary
                                    :columns="$columns"
                                    extra-heading-column
                                    :placeholder-columns="false"
                                    :plural-model-label="$pluralModelLabel"
                                    :records="$records"
                                />
                            </tbody>
                        </table>
                    @endif
                @elseif ((! ($content || $hasColumnsLayout)) && ($records !== null))
                    <table
                        @if ($isReorderable)
                            x-on:end.stop="
                                $wire.reorderTable(
                                    $event.target.sortable.toArray(),
                                    $event.item.getAttribute('x-sortable-item'),
                                )
                            "
                            x-sortable
                            data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                        @endif
                        class="fi-ta-table"
                    >
                        <thead>
                            @if ($hasColumnGroups)
                                <tr class="fi-ta-table-head-groups-row">
                                    @if (count($records))
                                        @if ($isReordering)
                                            <th></th>
                                        @else
                                            @if (count($actions) && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
                                                <th></th>
                                            @endif

                                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                                <th></th>
                                            @endif
                                        @endif
                                    @endif

                                    @foreach ($columnsLayout as $columnGroup)
                                        @if ($columnGroup instanceof Column)
                                            @if ($columnGroup->isVisible() && (! $columnGroup->isToggledHidden()))
                                                <th></th>
                                            @endif
                                        @elseif ($columnGroup instanceof ColumnGroup)
                                            @php
                                                $columnGroupColumnsCount = count($columnGroup->getVisibleColumns());
                                            @endphp

                                            @if ($columnGroupColumnsCount)
                                                <th
                                                    colspan="{{ $columnGroupColumnsCount }}"
                                                    {{
                                                        $columnGroup->getExtraHeaderAttributeBag()->class([
                                                            'fi-ta-header-group-cell',
                                                            'fi-wrapped' => $columnGroup->isHeaderWrapped(),
                                                            ((($columnGroupAlignment = $columnGroup->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$columnGroupAlignment->value}" : (is_string($columnGroupAlignment) ? $columnGroupAlignment : '')),
                                                            (filled($columnGroupHiddenFrom = $columnGroup->getHiddenFrom()) ? "{$columnGroupHiddenFrom}:fi-hidden" : ''),
                                                            (filled($columnGroupVisibleFrom = $columnGroup->getVisibleFrom()) ? "{$columnGroupVisibleFrom}:fi-visible" : ''),
                                                        ])
                                                    }}
                                                >
                                                    {{ $columnGroup->getLabel() }}
                                                </th>
                                            @endif
                                        @endif
                                    @endforeach

                                    @if ((! $isReordering) && count($records))
                                        @if (count($actions) && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
                                            <th></th>
                                        @endif

                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                            <th></th>
                                        @endif
                                    @endif
                                </tr>
                            @endif

                            <tr>
                                @if (count($records))
                                    @if ($isReordering)
                                        <th></th>
                                    @else
                                        @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                            @if ($actionsColumnLabel)
                                                <th class="fi-ta-header-cell">
                                                    {{ $actionsColumnLabel }}
                                                </th>
                                            @else
                                                <th
                                                    aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatActionsCount) }}"
                                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                ></th>
                                            @endif
                                        @endif

                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                            <th
                                                class="fi-ta-cell fi-ta-selection-cell"
                                            >
                                                <input
                                                    aria-label="{{ __('filament-tables::table.fields.bulk_select_page.label') }}"
                                                    type="checkbox"
                                                    x-bind:checked="
                                                        const recordsOnPage = getRecordsOnPage()

                                                        if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                            $el.checked = true

                                                            return 'checked'
                                                        }

                                                        $el.checked = false

                                                        return null
                                                    "
                                                    x-on:click="toggleSelectRecordsOnPage"
                                                    {{-- Make sure the "checked" state gets re-evaluated after a Livewire request: --}}
                                                    wire:key="{{ $this->getId() }}.table.bulk-select-page.checkbox.{{ \Illuminate\Support\Str::random() }}"
                                                    wire:loading.attr="disabled"
                                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                    class="fi-ta-page-checkbox fi-checkbox-input"
                                                />
                                            </th>
                                        @endif

                                        @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                            @if ($actionsColumnLabel)
                                                <th class="fi-ta-header-cell">
                                                    {{ $actionsColumnLabel }}
                                                </th>
                                            @else
                                                <th
                                                    aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatActionsCount) }}"
                                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                ></th>
                                            @endif
                                        @endif
                                    @endif
                                @endif

                                @foreach ($columns as $column)
                                    @php
                                        $columnName = $column->getName();
                                        $columnLabel = $column->getLabel();
                                        $columnAlignment = $column->getAlignment();
                                        $columnWidth = $column->getWidth();
                                        $isColumnActivelySorted = $getSortColumn() === $column->getName();
                                        $isColumnSortable = $column->isSortable() && (! $isReordering);
                                    @endphp

                                    <th
                                        @if ($isColumnActivelySorted)
                                            aria-sort="{{ $sortDirection === 'asc' ? 'ascending' : 'descending' }}"
                                        @endif
                                        {{
                                            $column->getExtraHeaderAttributeBag()
                                                ->class([
                                                    'fi-ta-header-cell',
                                                    'fi-ta-header-cell-' . str($columnName)->camel()->kebab(),
                                                    'fi-growable' => blank($columnWidth) && $column->canGrow(default: false),
                                                    'fi-grouped' => $column->getGroup(),
                                                    'fi-wrapped' => $column->isHeaderWrapped(),
                                                    'fi-ta-header-cell-sorted' => $isColumnActivelySorted,
                                                    ((($columnAlignment = $column->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
                                                    (filled($columnHiddenFrom = $column->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
                                                    (filled($columnVisibleFrom = $column->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
                                                ])
                                                ->style([
                                                    ('width: ' . $columnWidth) => filled($columnWidth),
                                                ])
                                        }}
                                    >
                                        @if ($isColumnSortable)
                                            <button
                                                aria-label="{{ trim(strip_tags($columnLabel)) }}"
                                                type="button"
                                                wire:click="sortTable('{{ $columnName }}')"
                                                wire:loading.attr="disabled"
                                                class="fi-ta-header-cell-sort-btn"
                                            >
                                                {{ $columnLabel }}

                                                {{
                                                    \Filament\Support\generate_icon_html(($isColumnActivelySorted && $sortDirection === 'asc') ? \Filament\Support\Icons\Heroicon::ChevronUp : \Filament\Support\Icons\Heroicon::ChevronDown, alias: match (true) {
                                                        $isColumnActivelySorted && ($sortDirection === 'asc') => 'tables::header-cell.sort-asc-button',
                                                        $isColumnActivelySorted && ($sortDirection === 'desc') => 'tables::header-cell.sort-desc-button',
                                                        default => 'tables::header-cell.sort-button',
                                                    })
                                                }}
                                            </button>
                                        @else
                                            {{ $columnLabel }}
                                        @endif
                                    </th>
                                @endforeach

                                @if ((! $isReordering) && count($records))
                                    @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns)
                                        @if ($actionsColumnLabel)
                                            <th
                                                class="fi-ta-header-cell fi-align-end"
                                            >
                                                {{ $actionsColumnLabel }}
                                            </th>
                                        @else
                                            <th
                                                aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatActionsCount) }}"
                                                class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                            ></th>
                                        @endif
                                    @endif

                                    @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                        <th
                                            class="fi-ta-cell fi-ta-selection-cell"
                                        >
                                            <input
                                                aria-label="{{ __('filament-tables::table.fields.bulk_select_page.label') }}"
                                                type="checkbox"
                                                x-bind:checked="
                                                    const recordsOnPage = getRecordsOnPage()

                                                    if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                        $el.checked = true

                                                        return 'checked'
                                                    }

                                                    $el.checked = false

                                                    return null
                                                "
                                                x-on:click="toggleSelectRecordsOnPage"
                                                {{-- Make sure the "checked" state gets re-evaluated after a Livewire request: --}}
                                                wire:key="{{ $this->getId() }}.table.bulk-select-page.checkbox.{{ \Illuminate\Support\Str::random() }}"
                                                wire:loading.attr="disabled"
                                                wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                class="fi-ta-page-checkbox fi-checkbox-input"
                                            />
                                        </th>
                                    @endif

                                    @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                        @if ($actionsColumnLabel)
                                            <th
                                                class="fi-ta-header-cell fi-align-end"
                                            >
                                                {{ $actionsColumnLabel }}
                                            </th>
                                        @else
                                            <th
                                                aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatActionsCount) }}"
                                                class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                            ></th>
                                        @endif
                                    @endif
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @if ($isColumnSearchVisible)
                                <tr class="fi-ta-row fi-ta-row-not-reorderable">
                                    @if (count($records))
                                        @if ($isReordering)
                                            <td></td>
                                        @else
                                            @if (count($actions) && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
                                                <td></td>
                                            @endif

                                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                                <td></td>
                                            @endif
                                        @endif
                                    @endif

                                    @foreach ($columns as $column)
                                        @php
                                            $columnName = $column->getName();
                                        @endphp

                                        <td
                                            @class([
                                                'fi-ta-cell',
                                                'fi-ta-individual-search-cell' => $isIndividuallySearchable = $column->isIndividuallySearchable(),
                                                'fi-ta-individual-search-cell-' . str($columnName)->camel()->kebab() => $isIndividuallySearchable,
                                            ])
                                        >
                                            @if ($isIndividuallySearchable)
                                                <x-filament-tables::search-field
                                                    :debounce="$searchDebounce"
                                                    :on-blur="$isSearchOnBlur"
                                                    :wire-model="'tableColumnSearches.' . $columnName"
                                                />
                                            @endif
                                        </td>
                                    @endforeach

                                    @if ((! $isReordering) && count($records))
                                        @if (count($actions) && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
                                            <td></td>
                                        @endif

                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                            <td></td>
                                        @endif
                                    @endif
                                </tr>
                            @endif

                            @if (count($records))
                                @php
                                    $isRecordRowStriped = false;
                                    $previousRecord = null;
                                    $previousRecordGroupKey = null;
                                    $previousRecordGroupTitle = null;
                                @endphp

                                @foreach ($records as $record)
                                    @php
                                        $recordAction = $getRecordAction($record);
                                        $recordKey = $getRecordKey($record);
                                        $recordUrl = $getRecordUrl($record);
                                        $openRecordUrlInNewTab = $shouldOpenRecordUrlInNewTab($record);
                                        $recordGroupKey = $group?->getStringKey($record);
                                        $recordGroupTitle = $group?->getTitle($record);

                                        $recordActions = array_reduce(
                                            $actions,
                                            function (array $carry, $action) use ($record): array {
                                                if (! $action instanceof \Filament\Actions\ActionGroup) {
                                                    $action = clone $action;
                                                }

                                                if (! $action instanceof \Filament\Actions\BulkAction) {
                                                    $action->record($record);
                                                }

                                                if ($action->isHidden()) {
                                                    return $carry;
                                                }

                                                $carry[] = $action;

                                                return $carry;
                                            },
                                            initial: [],
                                        );
                                    @endphp

                                    @if ($recordGroupTitle !== $previousRecordGroupTitle)
                                        @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
                                            @php
                                                $groupColumn = $group->getColumn();
                                                $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                            @endphp

                                            <x-filament-tables::summary.row
                                                :actions="count($actions)"
                                                :actions-position="$actionsPosition"
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
                                            <tr
                                                class="fi-ta-row fi-ta-group-header-row"
                                            >
                                                @php
                                                    $isRecordGroupCollapsible = $group?->isCollapsible();
                                                    $groupHeaderColspan = $columnsCount;

                                                    if ($isSelectionEnabled) {
                                                        $groupHeaderColspan--;

                                                        if (
                                                            ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) &&
                                                            count($actions) &&
                                                            ($actionsPosition === ActionsPosition::BeforeCells)
                                                        ) {
                                                            $groupHeaderColspan--;
                                                        }
                                                    }
                                                @endphp

                                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                                        <td></td>
                                                    @endif

                                                    <td
                                                        class="fi-ta-cell fi-ta-group-selection-cell"
                                                    >
                                                        <input
                                                            aria-label="{{ __('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle]) }}"
                                                            type="checkbox"
                                                            x-bind:checked="
                                                                const recordsInGroup = getRecordsInGroupOnPage(@js($recordGroupKey))

                                                                if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                                                    $el.checked = true

                                                                    return 'checked'
                                                                }

                                                                $el.checked = false

                                                                return null
                                                            "
                                                            x-on:click="toggleSelectRecordsInGroup(@js($recordGroupKey))"
                                                            wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                                                            wire:loading.attr="disabled"
                                                            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                            class="fi-ta-record-checkbox fi-ta-group-checkbox fi-checkbox-input"
                                                        />
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
                                                            <{{ $secondLevelHeadingTag }}
                                                                class="fi-ta-group-heading"
                                                            >
                                                                @if (filled($recordGroupLabel = ($group->isTitlePrefixedWithLabel() ? $group->getLabel() : null)))
                                                                        {{ $recordGroupLabel }}:
                                                                @endif

                                                                {{ $recordGroupTitle }}
                                                            </{{ $secondLevelHeadingTag }}>

                                                            @if (filled($recordGroupDescription = $group->getDescription($record, $recordGroupTitle)))
                                                                <p
                                                                    class="fi-ta-group-description"
                                                                >
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
                                                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, alias: 'tables::grouping.collapse-button', size: \Filament\Support\Enums\IconSize::Small) }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>

                                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                                    <td
                                                        class="fi-ta-cell fi-ta-group-selection-cell"
                                                    >
                                                        <input
                                                            aria-label="{{ __('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle]) }}"
                                                            type="checkbox"
                                                            x-bind:checked="
                                                                const recordsInGroup = getRecordsInGroupOnPage(@js($recordGroupKey))

                                                                if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                                                    $el.checked = true

                                                                    return 'checked'
                                                                }

                                                                $el.checked = false

                                                                return null
                                                            "
                                                            x-on:click="toggleSelectRecordsInGroup(@js($recordGroupKey))"
                                                            wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                                                            wire:loading.attr="disabled"
                                                            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                            class="fi-ta-record-checkbox fi-ta-group-checkbox fi-checkbox-input"
                                                        />
                                                    </td>
                                                @endif
                                            </tr>
                                        @endif

                                        @php
                                            $isRecordRowStriped = false;
                                        @endphp
                                    @endif

                                    @if (! $isGroupsOnly)
                                        <tr
                                            wire:key="{{ $this->getId() }}.table.records.{{ $recordKey }}"
                                            {{ $isReordering ? 'x-sortable-handle' : null }}
                                            {{ $isReordering ? "x-sortable-item=\"{$recordKey}\"" : null }}
                                            x-bind:class="{
                                                {{ $group?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . \Illuminate\Support\Js::from($recordGroupTitle) . '),' : '' }}
                                                'fi-selected': isRecordSelected(@js($recordKey)),
                                            }"
                                            @class([
                                                'fi-ta-row',
                                                'fi-clickable' => $recordAction || $recordUrl,
                                                'fi-striped' => $isStriped && $isRecordRowStriped,
                                                ...$getRecordClasses($record),
                                            ])
                                        >
                                            @if ($isReordering)
                                                <td class="fi-ta-cell">
                                                    <button
                                                        class="fi-ta-reorder-handle fi-icon-btn"
                                                        type="button"
                                                    >
                                                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: 'tables::reorder.handle') }}
                                                    </button>
                                                </td>
                                            @endif

                                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells && (! $isReordering))
                                                <td class="fi-ta-cell">
                                                    <div
                                                        @class([
                                                            'fi-ta-actions',
                                                            match ($actionsAlignment) {
                                                                Alignment::Center => 'fi-align-center',
                                                                Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                Alignment::End, Alignment::Right => '',
                                                                default => is_string($actionsAlignment) ? $actionsAlignment : '',
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
                                                <td class="fi-ta-cell">
                                                    @if ($isRecordSelectable($record))
                                                        <input
                                                            aria-label="{{ __('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey]) }}"
                                                            type="checkbox"
                                                            value="{{ $recordKey }}"
                                                            x-model="selectedRecords"
                                                            data-group="{{ $recordGroupKey }}"
                                                            wire:loading.attr="disabled"
                                                            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                            class="fi-ta-record-checkbox fi-checkbox-input"
                                                        />
                                                    @endif
                                                </td>
                                            @endif

                                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns && (! $isReordering))
                                                <td class="fi-ta-cell">
                                                    <div
                                                        @class([
                                                            'fi-ta-actions',
                                                            match ($actionsAlignment) {
                                                                Alignment::Center => 'fi-align-center',
                                                                Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                Alignment::End, Alignment::Right => '',
                                                                default => is_string($actionsAlignment) ? $actionsAlignment : '',
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
                                                        if ($columnAction instanceof \Filament\Actions\Action) {
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
                                                    {{
                                                        $column->getExtraCellAttributeBag()->class([
                                                            'fi-ta-cell',
                                                            'fi-ta-cell-' . str($column->getName())->camel()->kebab(),
                                                            ((($columnAlignment = $column->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
                                                            ((($columnVerticalAlignment = $column->getVerticalAlignment()) instanceof \Filament\Support\Enums\VerticalAlignment) ? "fi-vertical-align-{$columnVerticalAlignment->value}" : (is_string($columnVerticalAlignment) ? $columnVerticalAlignment : '')),
                                                            (filled($columnHiddenFrom = $column->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
                                                            (filled($columnVisibleFrom = $column->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
                                                        ])
                                                    }}
                                                >
                                                    <{{ $columnWrapperTag }}
                                                        @if ($columnWrapperTag === 'a')
                                                            {{ \Filament\Support\generate_href_html($columnUrl ?: $recordUrl, $columnUrl ? $column->shouldOpenUrlInNewTab() : $openRecordUrlInNewTab) }}
                                                        @elseif ($columnWrapperTag === 'button')
                                                            type="button"
                                                            wire:click.stop.prevent="{{ $columnWireClickAction }}"
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
                                                </td>
                                            @endforeach

                                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns && (! $isReordering))
                                                <td class="fi-ta-cell">
                                                    <div
                                                        @class([
                                                            'fi-ta-actions',
                                                            match ($actionsAlignment) {
                                                                Alignment::Center => 'fi-align-center',
                                                                Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                Alignment::End, Alignment::Right => '',
                                                                default => is_string($actionsAlignment) ? $actionsAlignment : '',
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
                                                <td class="fi-ta-cell">
                                                    @if ($isRecordSelectable($record))
                                                        <input
                                                            aria-label="{{ __('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey]) }}"
                                                            type="checkbox"
                                                            value="{{ $recordKey }}"
                                                            x-model="selectedRecords"
                                                            data-group="{{ $recordGroupKey }}"
                                                            wire:loading.attr="disabled"
                                                            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                            class="fi-ta-record-checkbox fi-checkbox-input"
                                                        />
                                                    @endif
                                                </td>
                                            @endif

                                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells && (! $isReordering))
                                                <td class="fi-ta-cell">
                                                    <div
                                                        @class([
                                                            'fi-ta-actions',
                                                            match ($actionsAlignment) {
                                                                Alignment::Center => 'fi-align-center',
                                                                Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                Alignment::End, Alignment::Right => '',
                                                                default => is_string($actionsAlignment) ? $actionsAlignment : '',
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

                                    @php
                                        $isRecordRowStriped = ! $isRecordRowStriped;
                                        $previousRecord = $record;
                                        $previousRecordGroupKey = $recordGroupKey;
                                        $previousRecordGroupTitle = $recordGroupTitle;
                                    @endphp
                                @endforeach

                                @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                                    @php
                                        $groupColumn = $group->getColumn();
                                        $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                    @endphp

                                    <x-filament-tables::summary.row
                                        :actions="count($actions)"
                                        :actions-position="$actionsPosition"
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

                                @if ($hasSummary && (! $isReordering))
                                    @php
                                        $groupColumn = $group?->getColumn();
                                    @endphp

                                    <x-filament-tables::summary
                                        :actions="count($actions)"
                                        :actions-position="$actionsPosition"
                                        :columns="$columns"
                                        :group-column="$groupColumn"
                                        :groups-only="$isGroupsOnly"
                                        :plural-model-label="$pluralModelLabel"
                                        :record-checkbox-position="$recordCheckboxPosition"
                                        :records="$records"
                                        :selection-enabled="$isSelectionEnabled"
                                    />
                                @endif
                            @endif
                        </tbody>

                        @if (($records !== null) && count($records) && $contentFooter)
                            <tfoot>
                                <tr>
                                    {{
                                        $contentFooter->with([
                                            'columns' => $columns,
                                            'records' => $records,
                                        ])
                                    }}
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                @elseif ($records === null)
                    <div class="fi-ta-table-loading-spacer"></div>
                @endif
            </div>
        @endif

        @if (($records !== null) && ! count($records))
            @if ($emptyState = $getEmptyState())
                {{ $emptyState }}
            @else
                <div class="fi-ta-empty-state">
                    <div class="fi-ta-empty-state-content">
                        <div class="fi-ta-empty-state-icon-bg">
                            {{ \Filament\Support\generate_icon_html($getEmptyStateIcon(), size: \Filament\Support\Enums\IconSize::Large) }}
                        </div>

                        <{{ $secondLevelHeadingTag }}
                            class="fi-ta-empty-state-heading"
                        >
                            {{ $getEmptyStateHeading() }}
                        </{{ $secondLevelHeadingTag }}>

                        @if ($emptyStateDescription = $getEmptyStateDescription())
                            <p class="fi-ta-empty-state-description">
                                {{ $description }}
                            </p>
                        @endif

                        @if ($emptyStateActions = array_filter(
                                 $getEmptyStateActions(),
                                 fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
                             ))
                            <div
                                class="fi-ta-actions fi-align-center fi-wrapped"
                            >
                                @foreach ($emptyStateActions as $action)
                                    {{ $action }}
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        @if ((($records instanceof \Illuminate\Contracts\Pagination\Paginator) || ($records instanceof \Illuminate\Contracts\Pagination\CursorPaginator)) &&
             ((! ($records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) || $records->total()))
            @php
                $hasExtremePaginationLinks = $hasExtremePaginationLinks();
                $paginationPageOptions = $getPaginationPageOptions();
            @endphp

            <x-filament::pagination
                :extreme-links="$hasExtremePaginationLinks"
                :page-options="$paginationPageOptions"
                :paginator="$records"
            />
        @endif

        @if ($hasFiltersBelowContent)
            <x-filament-tables::filters
                :apply-action="$filtersApplyAction"
                :form="$filtersForm"
                :heading-tag="$secondLevelHeadingTag"
                class="fi-ta-filters-below-content"
            />
        @endif
    </div>

    <x-filament-actions::modals />
</div>
