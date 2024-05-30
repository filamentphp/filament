@php
    use Filament\Tables\Enums\FiltersLayout;
@endphp

@props([
    'activeFiltersCount' => 0,
    'applyAction',
    'form',
    'layout',
    'maxHeight' => null,
    'triggerAction',
    'width' => 'xs',
])

@if (($layout === FiltersLayout::Modal) || $triggerAction->isModalSlideOver())
    <x-filament::modal
        :alignment="$triggerAction->getModalAlignment()"
        :autofocus="$triggerAction->isModalAutofocused()"
        :close-button="$triggerAction->hasModalCloseButton()"
        :close-by-clicking-away="$triggerAction->isModalClosedByClickingAway()"
        :close-by-escaping="$triggerAction?->isModalClosedByEscaping()"
        :description="$triggerAction->getModalDescription()"
        :footer-actions="$triggerAction->getVisibleModalFooterActions()"
        :footer-actions-alignment="$triggerAction->getModalFooterActionsAlignment()"
        :heading="$triggerAction->getCustomModalHeading() ?? __('filament-tables::table.filters.heading')"
        :icon="$triggerAction->getModalIcon()"
        :icon-color="$triggerAction->getModalIconColor()"
        :slide-over="$triggerAction->isModalSlideOver()"
        :sticky-footer="$triggerAction->isModalFooterSticky()"
        :sticky-header="$triggerAction->isModalHeaderSticky()"
        :width="$width"
        wire:key="{{ $this->getId() }}.table.filters"
        {{ $attributes->class(['fi-ta-filters-modal']) }}
    >
        <x-slot name="trigger">
            {{ $triggerAction->badge($activeFiltersCount) }}
        </x-slot>

        {{ $triggerAction->getModalContent() }}

        {{ $form }}

        {{ $triggerAction->getModalContentFooter() }}
    </x-filament::modal>
@else
    <x-filament::dropdown
        :max-height="$maxHeight"
        placement="bottom-end"
        shift
        :width="$width"
        wire:key="{{ $this->getId() }}.table.filters"
        {{ $attributes->class(['fi-ta-filters-dropdown']) }}
    >
        <x-slot name="trigger">
            {{ $triggerAction->badge($activeFiltersCount) }}
        </x-slot>

        <x-filament-tables::filters
            :apply-action="$applyAction"
            :form="$form"
            class="p-6"
        />
    </x-filament::dropdown>
@endif
