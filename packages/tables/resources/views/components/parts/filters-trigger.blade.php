@php
    use Filament\Support\Enums\Width;
    use Filament\Tables\Enums\FiltersLayout;

    $filtersLayout = $table->getFiltersLayout();
    $hasFiltersDialog = $table->hasFiltersDialog();
    $hasFiltersBeforeOrAfterContent = $table->isFilterable() && in_array($filtersLayout, [FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible]);
    $hasCollapsibleFilters = $table->isFilterable() && in_array($filtersLayout, [FiltersLayout::AboveContentCollapsible, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContentCollapsible]);
    $filtersTriggerAction = $table->getFiltersTriggerAction();
    $activeFiltersCount = $table->getActiveFiltersCount();
    $filtersFormWidth = $table->getFiltersFormWidth();

    if (is_string($filtersFormWidth)) {
        $filtersFormWidth = Width::tryFrom($filtersFormWidth) ?? $filtersFormWidth;
    }
@endphp

@if ($hasFiltersDialog)
    @if (($filtersLayout === FiltersLayout::Modal) || $filtersTriggerAction->isModalSlideOver())
        @php
            $filtersTriggerActionModalAlignment = $filtersTriggerAction->getModalAlignment();
            $filtersTriggerActionIsModalAutofocused = $filtersTriggerAction->isModalAutofocused();
            $filtersTriggerActionHasModalCloseButton = $filtersTriggerAction->hasModalCloseButton();
            $filtersTriggerActionIsModalClosedByClickingAway = $filtersTriggerAction->isModalClosedByClickingAway();
            $filtersTriggerActionIsModalClosedByEscaping = $filtersTriggerAction->isModalClosedByEscaping();
            $filtersTriggerActionModalDescription = $filtersTriggerAction->getModalDescription();
            $filtersTriggerActionExtraModalWindowAttributeBag = $filtersTriggerAction->getExtraModalWindowAttributeBag();
            $filtersTriggerActionExtraModalOverlayAttributeBag = $filtersTriggerAction->getExtraModalOverlayAttributeBag();
            $filtersTriggerActionVisibleModalFooterActions = $filtersTriggerAction->getVisibleModalFooterActions();
            $filtersTriggerActionModalFooterActionsAlignment = $filtersTriggerAction->getModalFooterActionsAlignment();
            $filtersTriggerActionModalHeading = $filtersTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.filters.heading');
            $filtersTriggerActionModalIcon = $filtersTriggerAction->getModalIcon();
            $filtersTriggerActionModalIconColor = $filtersTriggerAction->getModalIconColor();
            $filtersTriggerActionIsModalSlideOver = $filtersTriggerAction->isModalSlideOver();
            $filtersTriggerActionModalSlideOverPosition = $filtersTriggerAction->getModalSlideOverPosition();
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
            :extra-modal-window-attribute-bag="$filtersTriggerActionExtraModalWindowAttributeBag"
            :extra-modal-overlay-attribute-bag="$filtersTriggerActionExtraModalOverlayAttributeBag"
            :footer-actions="$filtersTriggerActionVisibleModalFooterActions"
            :footer-actions-alignment="$filtersTriggerActionModalFooterActionsAlignment"
            :heading="$filtersTriggerActionModalHeading"
            :icon="$filtersTriggerActionModalIcon"
            :icon-color="$filtersTriggerActionModalIconColor"
            :slide-over="$filtersTriggerActionIsModalSlideOver"
            :slide-over-position="$filtersTriggerActionModalSlideOverPosition"
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

            {{ $table->getFiltersForm() }}

            {{ $filtersTriggerAction->getModalContentFooter() }}
        </x-filament::modal>
    @else
        <x-filament::dropdown
            :max-height="$table->getFiltersFormMaxHeight()"
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
                :apply-action="$table->getFiltersApplyAction()"
                :form="$table->getFiltersForm()"
                :heading-tag="$table->getSecondLevelHeadingTag()"
                :reset-action-position="$table->getFiltersResetActionPosition()"
            />
        </x-filament::dropdown>
    @endif
@elseif ($hasFiltersBeforeOrAfterContent)
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
