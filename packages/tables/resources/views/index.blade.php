@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Enums\Width;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Actions\HeaderActionsPosition;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\ColumnGroup;
    use Filament\Tables\Enums\ColumnManagerResetActionPosition;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\FiltersResetActionPosition;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Filament\Tables\View\TablesRenderHook;
    use Illuminate\Support\Str;
    use Illuminate\View\ComponentAttributeBag;

    $defaultRecordActions = $getRecordActions();
    $flatRecordActionsCount = count($getFlatRecordActions());
    $recordActionsAlignment = $getRecordActionsAlignment();
    $recordActionsPosition = $getRecordActionsPosition();
    $recordActionsColumnLabel = $getRecordActionsColumnLabel();

    if (! $recordActionsAlignment instanceof Alignment) {
        $recordActionsAlignment = filled($recordActionsAlignment) ? (Alignment::tryFrom($recordActionsAlignment) ?? $recordActionsAlignment) : null;
    }

    $activeFiltersCount = $getActiveFiltersCount();
    $isSelectionDisabled = $isSelectionDisabled();
    $maxSelectableRecords = $getMaxSelectableRecords();
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
    $filtersResetActionPosition = $getFiltersResetActionPosition();
    $columnManagerResetActionPosition = $getColumnManagerResetActionPosition();
    $hasColumnGroups = $hasColumnGroups();
    $hasColumnsLayout = $hasColumnsLayout();
    $hasPageSummary = $hasPageSummary();
    $hasAllTableSummary = $hasAllTableSummary();
    $hasSummary = ($hasPageSummary || $hasAllTableSummary) && $hasSummary($this->getAllTableSummaryQuery());
    $header = $getHeader();
    $headerActions = array_filter(
        $getHeaderActions(),
        fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $toolbarActions = array_filter(
        $getToolbarActions(),
        fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );

    $hasNonBulkToolbarAction = false;

    foreach ($toolbarActions as $toolbarAction) {
        if ($toolbarAction instanceof \Filament\Actions\BulkActionGroup) {
            continue;
        }

        if ($toolbarAction instanceof \Filament\Actions\ActionGroup) {
            if ($toolbarAction->hasNonBulkAction()) {
                $hasNonBulkToolbarAction = true;

                break;
            }

            continue;
        }

        if (! $toolbarAction->isBulk()) {
            $hasNonBulkToolbarAction = true;

            break;
        }
    }

    $groups = $getGroups();
    $description = $getDescription();
    $isGroupsOnly = $isGroupsOnly() && $group;
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $areGroupingSettingsVisible = (! $isReordering) && count($groups) && (! $areGroupingSettingsHidden());
    $isGroupingDirectionSettingHidden = $isGroupingDirectionSettingHidden();
    $areGroupsCollapsedByDefault = $areGroupsCollapsedByDefault();
    $areGroupingSettingsInDropdownOnDesktop = $areGroupingSettingsInDropdownOnDesktop();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSearchOnBlur = $isSearchOnBlur();
    $isSelectionEnabled = $isSelectionEnabled() && (! $isGroupsOnly);
    $selectsCurrentPageOnly = $selectsCurrentPageOnly();
    $selectsGroupsOnly = $selectsGroupsOnly();
    $recordCheckboxPosition = $getRecordCheckboxPosition();
    $isStriped = $isStriped();
    $isLoaded = $isLoaded();
    $hasFilters = $isFilterable();
    $filtersLayout = $getFiltersLayout();
    $filtersTriggerAction = $getFiltersTriggerAction();
    $hasFiltersDialog = $hasFilters && in_array($filtersLayout, [FiltersLayout::Dropdown, FiltersLayout::Modal]);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersBelowContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $hasFiltersBeforeContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible]);
    $hasFiltersAfterContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible]);
    $hasCollapsibleFilters = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContentCollapsible, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContentCollapsible]);
    $hasFiltersTrigger = $hasFilters && ($hasFiltersDialog || $hasFiltersBeforeContent || $hasFiltersAfterContent);
    $filtersFormMaxHeight = $getFiltersFormMaxHeight();
    $hasColumnManagerDropdown = $hasColumnManager();
    $hasReorderableColumns = $hasReorderableColumns();
    $hasToggleableColumns = $hasToggleableColumns();
    $columnManagerApplyAction = $getColumnManagerApplyAction();
    $columnManagerTriggerAction = $getColumnManagerTriggerAction();
    $hasHeader = $header || $heading || $description || ($headerActions && (! $isReordering)) || $isReorderable || $areGroupingSettingsVisible || $isGlobalSearchVisible || $hasFilters || count($filterIndicators) || $hasColumnManagerDropdown;
    $hasHeaderToolbar = $isReorderable || $areGroupingSettingsVisible || $isGlobalSearchVisible || $hasFiltersTrigger || $hasColumnManagerDropdown;
    $headingTag = $getHeadingTag();
    $secondLevelHeadingTag = $heading ? $getHeadingTag(1) : $headingTag;
    $pluralModelLabel = $getPluralModelLabel();
    $records = $isLoaded ? $getRecords() : null;
    $hasPagination = (($records instanceof \Illuminate\Contracts\Pagination\Paginator) || ($records instanceof \Illuminate\Contracts\Pagination\CursorPaginator)) && ((! ($records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) || $records->total());
    $hasEmptyState = ($records !== null) && ! count($records);
    $hasContentLayout = $content || $hasColumnsLayout;
    $searchDebounce = $getSearchDebounce();
    $allSelectableRecordsCount = ($isSelectionEnabled && $isLoaded) ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);
    $reorderRecordsTriggerAction = $getReorderRecordsTriggerAction($isReordering);
    $page = $this->getTablePage();
    $defaultSortOptionLabel = $getDefaultSortOptionLabel();
    $sortDirection = $getSortDirection();

    if (count($defaultRecordActions) && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
    }

    if ($group) {
        $groupedSummarySelectedState = $this->getTableSummarySelectedState($this->getAllTableSummaryQuery(), modifyQueryUsing: fn (\Illuminate\Database\Query\Builder $query) => $group->groupQuery($query, model: $getQuery()->getModel()));
    }

    if (is_string($filtersFormWidth)) {
        $filtersFormWidth = Width::tryFrom($filtersFormWidth) ?? $filtersFormWidth;
    }
@endphp

<div
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
    x-data="filamentTable({
                areGroupsCollapsedByDefault: @js($areGroupsCollapsedByDefault),
                canTrackDeselectedRecords: @js($canTrackDeselectedRecords()),
                currentSelectionLivewireProperty: @js($getCurrentSelectionLivewireProperty()),
                maxSelectableRecords: @js($maxSelectableRecords),
                selectsCurrentPageOnly: @js($selectsCurrentPageOnly),
                $wire,
            })"
    {{
        $getExtraAttributeBag()->class([
            'fi-ta',
            'fi-loading' => $records === null,
        ])
    }}
>
    <input
        type="hidden"
        value="{{ $allSelectableRecordsCount }}"
        x-ref="allSelectableRecordsCount"
    />

    <div
        @class([
            'fi-ta-ctn',
            'fi-ta-ctn-with-content-layout' => $hasContentLayout,
            'fi-ta-ctn-with-footer' => $hasPagination || $hasEmptyState || $hasFiltersBelowContent,
            'fi-ta-ctn-with-header' => $hasHeader,
        ])
    >
        @if ($hasFiltersBeforeContent)
            <div
                x-ref="filtersContentContainer"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-bind:class="{ 'fi-open': areFiltersOpen }"
                @class([
                    'fi-ta-filters-before-content-ctn',
                    'lg:fi-open' => ! $hasCollapsibleFilters,
                    (($filtersFormWidth ??= Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : (is_string($filtersFormWidth) ? $filtersFormWidth : null),
                ])
            >
                <x-filament-tables::filters
                    :apply-action="$filtersApplyAction"
                    :form="$filtersForm"
                    :heading-tag="$secondLevelHeadingTag"
                    class="fi-ta-filters-before-content"
                    :reset-action-position="$filtersResetActionPosition"
                />
            </div>
        @endif

        <div class="fi-ta-main">
            <div
                @if (! $hasHeader) x-cloak @endif
                x-show="@js($hasHeader) || @js($hasNonBulkToolbarAction) || (getSelectedRecordsCount() && @js(count($toolbarActions)))"
                class="fi-ta-header-ctn"
            >
                {{ FilamentView::renderHook(TablesRenderHook::HEADER_BEFORE, scopes: static::class) }}

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
                            <div
                                class="fi-ta-actions fi-align-start fi-wrapped"
                            >
                                @foreach ($headerActions as $action)
                                    {{ $action }}
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{ FilamentView::renderHook(TablesRenderHook::HEADER_AFTER, scopes: static::class) }}

                @if ($hasFiltersAboveContent)
                    <div
                        @if ($hasCollapsibleFilters)
                            x-bind:class="{ 'fi-open': areFiltersOpen }"
                        @endif
                        @class([
                            'fi-ta-filters-above-content-ctn',
                        ])
                    >
                        <x-filament-tables::filters
                            :apply-action="$filtersApplyAction"
                            :form="$filtersForm"
                            :heading-tag="$secondLevelHeadingTag"
                            x-cloak
                            :x-show="$hasCollapsibleFilters ? 'areFiltersOpen' : null"
                            :reset-action-position="$filtersResetActionPosition"
                        />

                        @if ($hasCollapsibleFilters)
                            <span
                                x-on:click="areFiltersOpen = ! areFiltersOpen"
                                class="fi-ta-filters-trigger-action-ctn"
                            >
                                {{ $filtersTriggerAction->badge($activeFiltersCount) }}
                            </span>
                        @endif
                    </div>
                @endif

                {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_BEFORE, scopes: static::class) }}

                <div
                    @if (! $hasHeaderToolbar) x-cloak @endif
                    x-show="@js($hasHeaderToolbar) || @js($hasNonBulkToolbarAction) || (getSelectedRecordsCount() && @js(count($toolbarActions)))"
                    class="fi-ta-header-toolbar"
                >
                    {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_START, scopes: static::class) }}

                    <div class="fi-ta-actions fi-align-start fi-wrapped">
                        {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_BEFORE, scopes: static::class) }}

                        @if ($isReorderable)
                            {{ $reorderRecordsTriggerAction }}
                        @endif

                        {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, scopes: static::class) }}

                        @if ((! $isReordering) && count($toolbarActions))
                            @foreach ($toolbarActions as $action)
                                {{ $action }}
                            @endforeach
                        @endif

                        {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE, scopes: static::class) }}

                        @if ($areGroupingSettingsVisible)
                            <div
                                x-data="{
                                    grouping: $wire.$entangle('tableGrouping', true),
                                    group: null,
                                    direction: null,
                                }"
                                x-init="
                                    if (grouping) {
                                        ;[group, direction] = grouping.split(':')
                                        direction ??= 'asc'
                                    }

                                    $watch('grouping', function () {
                                        if (! grouping) {
                                            group = null
                                            direction = null

                                            return
                                        }

                                        ;[group, direction] = grouping.split(':')
                                        direction ??= 'asc'
                                    })

                                    $watch('direction', function () {
                                        grouping = group ? `${group}:${direction}` : null
                                    })

                                    $watch('group', function (newGroup, oldGroup) {
                                        if (! newGroup) {
                                            direction = null
                                            grouping = group ? `${group}:${direction}` : null

                                            return
                                        }

                                        if (oldGroup) {
                                            grouping = group ? `${group}:${direction}` : null

                                            return
                                        }

                                        direction ??= 'asc'
                                        grouping = group ? `${group}:${direction}` : null
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
                                            <x-filament::input.wrapper
                                                :prefix="__('filament-tables::table.grouping.fields.group.label')"
                                            >
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

                        {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER, scopes: static::class) }}
                    </div>

                    @if ($isGlobalSearchVisible || $hasFiltersTrigger || $hasColumnManagerDropdown)
                        <div>
                            {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE, scopes: static::class) }}

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

                            {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_AFTER, scopes: static::class) }}

                            @if ($hasFiltersTrigger || $hasColumnManagerDropdown)
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
                                        <x-filament::dropdown
                                            :max-height="$filtersFormMaxHeight"
                                            placement="bottom-end"
                                            shift
                                            :flip="false"
                                            :width="$filtersFormWidth ?? Width::ExtraSmall"
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
                                                :reset-action-position="$filtersResetActionPosition"
                                            />
                                        </x-filament::dropdown>
                                    @endif
                                @elseif ($hasFiltersBeforeContent || $hasFiltersAfterContent)
                                    <span
                                        x-ref="filtersTriggerActionContainer"
                                        x-on:click="toggleFiltersDropdown"
                                        @class([
                                            'fi-ta-filters-trigger-action-ctn',
                                            'lg:fi-hidden' => ! $hasCollapsibleFilters,
                                        ])
                                    >
                                        {{ $filtersTriggerAction->badge($activeFiltersCount) }}
                                    </span>
                                @endif

                                {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE, scopes: static::class) }}

                                @if ($hasColumnManagerDropdown)
                                    @php
                                        $columnManagerMaxHeight = $getColumnManagerMaxHeight();
                                        $columnManagerWidth = $getColumnManagerWidth();
                                        $columnManagerColumns = $getColumnManagerColumns();
                                    @endphp

                                    <x-filament::dropdown
                                        :max-height="$columnManagerMaxHeight"
                                        placement="bottom-end"
                                        shift
                                        :flip="false"
                                        :width="$columnManagerWidth"
                                        :wire:key="$this->getId() . '.table.column-manager'"
                                        class="fi-ta-col-manager-dropdown"
                                    >
                                        <x-slot name="trigger">
                                            {{ $columnManagerTriggerAction }}
                                        </x-slot>

                                        <x-filament-tables::column-manager
                                            :apply-action="$columnManagerApplyAction"
                                            :columns="$columnManagerColumns"
                                            :reset-action-position="$columnManagerResetActionPosition"
                                            :has-reorderable-columns="$hasReorderableColumns"
                                            :has-toggleable-columns="$hasToggleableColumns"
                                            :heading-tag="$secondLevelHeadingTag"
                                            :reorder-animation-duration="$getReorderAnimationDuration()"
                                        />
                                    </x-filament::dropdown>
                                @endif

                                {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER, scopes: static::class) }}
                            @endif
                        </div>
                    @endif

                    {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_END) }}
                </div>

                {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_AFTER) }}
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
            @elseif ($isSelectionEnabled && ($maxSelectableRecords !== 1) && $isLoaded)
                <div
                    x-cloak
                    x-bind:hidden="! getSelectedRecordsCount()"
                    x-show="getSelectedRecordsCount()"
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
                                window.pluralize(@js(__('filament-tables::table.selection_indicator.selected_count')), getSelectedRecordsCount(), {
                                    count: new Intl.NumberFormat(@js(str_replace('_', '-', app()->getLocale()))).format(getSelectedRecordsCount()),
                                })
                            "
                        ></span>
                    </div>

                    @if (! $isSelectionDisabled)
                        <div>
                            {{ FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE, scopes: static::class) }}

                            <div class="fi-ta-selection-indicator-actions-ctn">
                                @if (! $selectsGroupsOnly)
                                    <x-filament::link
                                        color="primary"
                                        tag="button"
                                        x-on:click="selectAllRecords"
                                        x-show="canSelectAllRecords()"
                                        {{-- Make sure the Alpine attributes get re-evaluated after a Livewire request: --}}
                                        :wire:key="$this->getId() . 'table.selection.indicator.actions.select-all.' . $allSelectableRecordsCount . '.' . $page"
                                    >
                                        {{ trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $allSelectableRecordsCount, ['count' => \Illuminate\Support\Number::format($allSelectableRecordsCount, locale: app()->getLocale())]) }}
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

            @if ($filterIndicators)
                @if (filled($filterIndicatorsView = FilamentView::renderHook(TablesRenderHook::FILTER_INDICATORS, scopes: static::class, data: ['filterIndicators' => $filterIndicators])))
                    {{ $filterIndicatorsView }}
                @else
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

                                    <x-filament::badge
                                        :color="$indicatorColor"
                                    >
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
                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::XMark, alias: \Filament\Tables\View\TablesIconAlias::FILTERS_REMOVE_ALL_BUTTON, size: \Filament\Support\Enums\IconSize::Small) }}
                            </button>
                        @endif
                    </div>
                @endif
            @endif

            @if (((! $content) && (! $hasColumnsLayout)) || ($records === null) || count($records))
                <div
                    @if ((! $isReordering) && ($pollingInterval = $getPollingInterval()))
                        wire:poll.{{ $pollingInterval }}
                    @endif
                    class="fi-ta-content-ctn fi-fixed-positioning-context"
                >
                    @if ($hasContentLayout && ($records !== null) && count($records))
                        @if (! $isReordering)
                            @php
                                $sortableColumns = array_filter(
                                    $columns,
                                    fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                                );
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
                                            $defaultRecordActions,
                                            function (array $carry, $action) use ($record): array {
                                                $action = $action->getClone();

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

                                                            return 'checked'
                                                        }

                                                        $el.checked = false

                                                        return null
                                                    "
                                                    wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                                                    wire:loading.attr="disabled"
                                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                    class="fi-ta-group-checkbox fi-checkbox-input"
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
                                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, alias: \Filament\Tables\View\TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: \Filament\Support\Enums\IconSize::Small) }}
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
                                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                            </button>
                                        @elseif ($isSelectionEnabled && $isRecordSelectable($record))
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
                                                wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                class="fi-ta-record-checkbox fi-checkbox-input"
                                            />
                                        @endif

                                        <div class="fi-ta-record-content-ctn">
                                            <div>
                                                @if ($recordUrl)
                                                    <a
                                                        {{ \Filament\Support\generate_href_html($recordUrl, $openRecordUrlInNewTab, hasNestedClickEventHandler: true) }}
                                                        {{ $getExtraRecordLinkAttributeBag($record)->class(['fi-ta-record-content']) }}
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
                                                        $recordWireClickAction = $getRecordAction($record)
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
                                                type="button"
                                                x-on:click="isCollapsed = ! isCollapsed"
                                                class="fi-ta-record-collapse-btn fi-icon-btn"
                                            >
                                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronDown, alias: \Filament\Tables\View\TablesIconAlias::COLUMNS_COLLAPSE_BUTTON) }}
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
                                        :all-table-summary="$hasAllTableSummary"
                                        :columns="$columns"
                                        extra-heading-column
                                        :page-summary="$hasPageSummary"
                                        :placeholder-columns="false"
                                        :plural-model-label="$pluralModelLabel"
                                        :records="$records"
                                    />
                                </tbody>
                            </table>
                        @endif
                    @elseif ((! ($content || $hasColumnsLayout)) && ($records !== null))
                        <table class="fi-ta-table">
                            <thead>
                                @if ($hasColumnGroups)
                                    <tr class="fi-ta-table-head-groups-row">
                                        @if (count($records))
                                            @if ($isReordering)
                                                <th></th>
                                            @else
                                                @if (count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
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
                                                                'fi-wrapped' => $columnGroup->canHeaderWrap(),
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
                                            @if (count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
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
                                            @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeCells)
                                                @if ($recordActionsColumnLabel)
                                                    <th
                                                        class="fi-ta-header-cell"
                                                    >
                                                        {{ $recordActionsColumnLabel }}
                                                    </th>
                                                @else
                                                    <th
                                                        aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                                                        class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                    ></th>
                                                @endif
                                            @endif

                                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                                <th
                                                    class="fi-ta-cell fi-ta-selection-cell"
                                                >
                                                    @if (($maxSelectableRecords !== 1) && (! $selectsGroupsOnly))
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
                                                </th>
                                            @endif

                                            @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeColumns)
                                                @if ($recordActionsColumnLabel)
                                                    <th
                                                        class="fi-ta-header-cell"
                                                    >
                                                        {{ $recordActionsColumnLabel }}
                                                    </th>
                                                @else
                                                    <th
                                                        aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
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
                                                $isColumnActivelySorted = $getSortColumn() === $column->getName();
                                                $isColumnSortable = $column->isSortable() && (! $isReordering);
                                                $columnHeaderTooltip = $column->getHeaderTooltip();
                                                $columnHeaderTooltipAttribute = ($columnHeaderTooltip instanceof \Illuminate\Contracts\Support\Htmlable)
                                                    ? 'x-tooltip.html'
                                                    : 'x-tooltip';
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
                                                            'fi-wrapped' => $column->canHeaderWrap(),
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
                                                    <span
                                                        aria-label="{{ trim(strip_tags($columnLabel)) }}"
                                                        role="button"
                                                        tabindex="0"
                                                        wire:click="sortTable('{{ $columnName }}')"
                                                        x-on:keydown.enter.prevent.stop="$wire.sortTable('{{ $columnName }}')"
                                                        x-on:keydown.space.prevent.stop="$wire.sortTable('{{ $columnName }}')"
                                                        wire:loading.attr="disabled"
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
                                                            \Filament\Support\generate_icon_html(($isColumnActivelySorted && $sortDirection === 'asc') ? \Filament\Support\Icons\Heroicon::ChevronUp : \Filament\Support\Icons\Heroicon::ChevronDown, alias: match (true) {
                                                                $isColumnActivelySorted && ($sortDirection === 'asc') => \Filament\Tables\View\TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON,
                                                                $isColumnActivelySorted && ($sortDirection === 'desc') => \Filament\Tables\View\TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON,
                                                                default => \Filament\Tables\View\TablesIconAlias::HEADER_CELL_SORT_BUTTON,
                                                            })
                                                        }}
                                                    </span>
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
                                        @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterColumns)
                                            @if ($recordActionsColumnLabel)
                                                <th
                                                    class="fi-ta-header-cell fi-align-end"
                                                >
                                                    {{ $recordActionsColumnLabel }}
                                                </th>
                                            @else
                                                <th
                                                    aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                ></th>
                                            @endif
                                        @endif

                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                            <th
                                                class="fi-ta-cell fi-ta-selection-cell"
                                            >
                                                @if (($maxSelectableRecords !== 1) && (! $selectsGroupsOnly))
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
                                            </th>
                                        @endif

                                        @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterCells)
                                            @if ($recordActionsColumnLabel)
                                                <th
                                                    class="fi-ta-header-cell fi-align-end"
                                                >
                                                    {{ $recordActionsColumnLabel }}
                                                </th>
                                            @else
                                                <th
                                                    aria-label="{{ trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount) }}"
                                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                ></th>
                                            @endif
                                        @endif
                                    @endif
                                </tr>
                            </thead>

                            @if ($isColumnSearchVisible || count($records))
                                <tbody
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
                                >
                                    @if ($isColumnSearchVisible)
                                        <tr
                                            class="fi-ta-row fi-ta-row-not-reorderable"
                                        >
                                            @if (count($records))
                                                @if ($isReordering)
                                                    <td></td>
                                                @else
                                                    @if (count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
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
                                                @if (count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
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
                                                    $defaultRecordActions,
                                                    function (array $carry, $action) use ($record): array {
                                                        $action = $action->getClone();

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

                                            @if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle)
                                                @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
                                                    @php
                                                        $groupColumn = $group->getColumn();
                                                        $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                                    @endphp

                                                    <x-filament-tables::summary.row
                                                        :actions="count($defaultRecordActions)"
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
                                                                    count($defaultRecordActions) &&
                                                                    ($recordActionsPosition === RecordActionsPosition::BeforeCells)
                                                                ) {
                                                                    $groupHeaderColspan--;
                                                                }
                                                            }
                                                        @endphp

                                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                                            @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeCells)
                                                                <td></td>
                                                            @endif

                                                            <td
                                                                class="fi-ta-cell fi-ta-group-selection-cell"
                                                            >
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

                                                                                return 'checked'
                                                                            }

                                                                            $el.checked = false

                                                                            return null
                                                                        "
                                                                        wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
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
                                                                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, alias: \Filament\Tables\View\TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: \Filament\Support\Enums\IconSize::Small) }}
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>

                                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                                            <td
                                                                class="fi-ta-cell fi-ta-group-selection-cell"
                                                            >
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

                                                                                return 'checked'
                                                                            }

                                                                            $el.checked = false

                                                                            return null
                                                                        "
                                                                        wire:key="{{ $this->getId() }}.table.bulk_select_group.checkbox.{{ $page }}"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                                        class="fi-ta-group-checkbox fi-checkbox-input"
                                                                    />
                                                                @endif
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
                                                    {!! $isReordering ? 'x-sortable-item="' . e($recordKey) . '"' : null !!}
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
                                                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                                            </button>
                                                        </td>
                                                    @endif

                                                    @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeCells && (! $isReordering))
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
                                                        <td
                                                            class="fi-ta-cell fi-ta-selection-cell"
                                                        >
                                                            @if ($isRecordSelectable($record))
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
                                                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                                    class="fi-ta-record-checkbox fi-checkbox-input"
                                                                />
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeColumns && (! $isReordering))
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
                                                                    {{ \Filament\Support\generate_href_html($columnUrl ?: $recordUrl, $columnUrl ? $column->shouldOpenUrlInNewTab() : $openRecordUrlInNewTab, hasNestedClickEventHandler: true) }}
                                                                    @if (blank($columnUrl) && filled($recordUrl))
                                                                        {{ $getExtraRecordLinkAttributeBag($record) }}
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
                                                        </td>
                                                    @endforeach

                                                    @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterColumns && (! $isReordering))
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
                                                        <td
                                                            class="fi-ta-cell fi-ta-selection-cell"
                                                        >
                                                            @if ($isRecordSelectable($record))
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
                                                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                                                    class="fi-ta-record-checkbox fi-checkbox-input"
                                                                />
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if (count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterCells && (! $isReordering))
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
                                                :actions="count($defaultRecordActions)"
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

                                        @if ($hasSummary && (! $isReordering))
                                            @php
                                                $groupColumn = $group?->getColumn();
                                            @endphp

                                            <x-filament-tables::summary
                                                :actions="count($defaultRecordActions)"
                                                :actions-position="$recordActionsPosition"
                                                :all-table-summary="$hasAllTableSummary"
                                                :columns="$columns"
                                                :group-column="$groupColumn"
                                                :groups-only="$isGroupsOnly"
                                                :page-summary="$hasPageSummary"
                                                :plural-model-label="$pluralModelLabel"
                                                :record-checkbox-position="$recordCheckboxPosition"
                                                :records="$records"
                                                :selection-enabled="$isSelectionEnabled"
                                            />
                                        @endif
                                    @endif
                                </tbody>
                            @endif

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
                        <div class="fi-ta-table-loading-ctn">
                            {{ \Filament\Support\generate_loading_indicator_html(size: \Filament\Support\Enums\IconSize::TwoExtraLarge) }}
                        </div>
                    @endif
                </div>
            @endif

            @if ($hasEmptyState)
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

                            @if (filled($emptyStateDescription = $getEmptyStateDescription()))
                                <p class="fi-ta-empty-state-description">
                                    {{ $emptyStateDescription }}
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

            @if ($hasPagination)
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
                    :reset-action-position="$filtersResetActionPosition"
                />
            @endif
        </div>

        @if ($hasFiltersAfterContent)
            <div
                x-ref="filtersContentContainer"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-bind:class="{ 'fi-open': areFiltersOpen }"
                @class([
                    'fi-ta-filters-after-content-ctn',
                    'lg:fi-open' => ! $hasCollapsibleFilters,
                    (($filtersFormWidth ??= Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : (is_string($filtersFormWidth) ? $filtersFormWidth : null),
                ])
            >
                <x-filament-tables::filters
                    :apply-action="$filtersApplyAction"
                    :form="$filtersForm"
                    :heading-tag="$secondLevelHeadingTag"
                    class="fi-ta-filters-after-content"
                    :reset-action-position="$filtersResetActionPosition"
                />
            </div>
        @endif
    </div>

    <x-filament-actions::modals />
</div>
