@php
    use Filament\Support\Enums\Width;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\TableFiltersPosition;

    $position = $part?->getPosition() ?? ($position ?? TableFiltersPosition::Auto);
    $hasFilters = $table->isFilterable();
    $filtersLayout = $table->getFiltersLayout();

    $isVisible = $hasFilters && match ($position) {
        TableFiltersPosition::Auto => true,
        TableFiltersPosition::Before => in_array($filtersLayout, [FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible]),
        TableFiltersPosition::Above => in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]),
        TableFiltersPosition::Below => $filtersLayout === FiltersLayout::BelowContent,
        TableFiltersPosition::After => in_array($filtersLayout, [FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible]),
    };

    $isCollapsible = ($position === TableFiltersPosition::Auto)
        ? $part->isCollapsible()
        : ($hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContentCollapsible, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContentCollapsible]));

    $filtersApplyAction = $table->getFiltersApplyAction();
    $filtersForm = $table->getFiltersForm();
    $filtersResetActionPosition = $table->getFiltersResetActionPosition();
    $headingTag = $table->getSecondLevelHeadingTag();

    $filtersFormWidth = $table->getFiltersFormWidth();

    if (is_string($filtersFormWidth)) {
        $filtersFormWidth = Width::tryFrom($filtersFormWidth) ?? $filtersFormWidth;
    }
@endphp

{{-- A single `@if` chain, so Livewire emits one pair of block markers like the original view did. --}}
@if ($isVisible && in_array($position, [TableFiltersPosition::Before, TableFiltersPosition::After]))
    <div
        wire:ignore.self
        x-ref="filtersContentContainer"
        x-transition:enter-start="fi-opacity-0"
        x-transition:leave-end="fi-opacity-0"
        x-bind:class="{ 'fi-open': areFiltersOpen }"
        @class([
            ($position === TableFiltersPosition::Before) ? 'fi-ta-filters-before-content-ctn' : 'fi-ta-filters-after-content-ctn',
            'lg:fi-open' => ! $isCollapsible,
            (($filtersFormWidth ??= Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : (is_string($filtersFormWidth) ? $filtersFormWidth : null),
        ])
    >
        <x-filament-tables::filters
            :apply-action="$filtersApplyAction"
            :form="$filtersForm"
            :heading-tag="$headingTag"
            :class="($position === TableFiltersPosition::Before) ? 'fi-ta-filters-before-content' : 'fi-ta-filters-after-content'"
            :reset-action-position="$filtersResetActionPosition"
        />
    </div>
@elseif ($isVisible && (($position === TableFiltersPosition::Above) || (($position === TableFiltersPosition::Auto) && $isCollapsible)))
    <div
        @if ($isCollapsible)
            x-bind:class="{ 'fi-open': areFiltersOpen }"
        @endif
        @class([
            'fi-ta-filters-above-content-ctn',
        ])
    >
        <x-filament-tables::filters
            :apply-action="$filtersApplyAction"
            :form="$filtersForm"
            :heading-tag="$headingTag"
            x-cloak
            :x-show="$isCollapsible ? 'areFiltersOpen' : null"
            :reset-action-position="$filtersResetActionPosition"
        />

        @if ($isCollapsible)
            <span
                x-on:click="areFiltersOpen = ! areFiltersOpen"
                class="fi-ta-filters-trigger-action-ctn"
            >
                {{ $table->getFiltersTriggerAction()->badge($table->getActiveFiltersCount()) }}
            </span>
        @endif
    </div>
@elseif ($isVisible && ($position === TableFiltersPosition::Below))
    <x-filament-tables::filters
        :apply-action="$filtersApplyAction"
        :form="$filtersForm"
        :heading-tag="$headingTag"
        class="fi-ta-filters-below-content"
        :reset-action-position="$filtersResetActionPosition"
    />
@elseif ($isVisible)
    <x-filament-tables::filters
        :apply-action="$filtersApplyAction"
        :form="$filtersForm"
        :heading-tag="$headingTag"
        :reset-action-position="$filtersResetActionPosition"
    />
@endif
