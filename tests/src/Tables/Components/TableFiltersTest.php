<?php

use Filament\Tables\Components\TableFilters;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\TableFiltersPosition;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

$positionContainerClasses = [
    'fi-ta-filters-before-content-ctn',
    'fi-ta-filters-above-content-ctn',
    'fi-ta-filters-below-content',
    'fi-ta-filters-after-content-ctn',
];

function renderPositionedTableFilters(FiltersLayout $filtersLayout): string
{
    return livewireTableWithParts(
        [
            TableFilters::make()->position(TableFiltersPosition::Before),
            TableFilters::make()->position(TableFiltersPosition::Above),
            TableFilters::make()->position(TableFiltersPosition::Below),
            TableFilters::make()->position(TableFiltersPosition::After),
        ],
        fn (Table $table): Table => $table->filtersLayout($filtersLayout),
    )->html();
}

it('renders the container for the `FiltersLayout` of the table', function (FiltersLayout $filtersLayout, string $containerClass) use ($positionContainerClasses): void {
    $html = renderPositionedTableFilters($filtersLayout);

    expect($html)->toContain($containerClass);

    foreach (array_diff($positionContainerClasses, [$containerClass]) as $otherContainerClass) {
        expect($html)->not->toContain($otherContainerClass);
    }
})->with([
    'above content' => [FiltersLayout::AboveContent, 'fi-ta-filters-above-content-ctn'],
    'above content collapsible' => [FiltersLayout::AboveContentCollapsible, 'fi-ta-filters-above-content-ctn'],
    'below content' => [FiltersLayout::BelowContent, 'fi-ta-filters-below-content'],
    'before content' => [FiltersLayout::BeforeContent, 'fi-ta-filters-before-content-ctn'],
    'before content collapsible' => [FiltersLayout::BeforeContentCollapsible, 'fi-ta-filters-before-content-ctn'],
    'after content' => [FiltersLayout::AfterContent, 'fi-ta-filters-after-content-ctn'],
    'after content collapsible' => [FiltersLayout::AfterContentCollapsible, 'fi-ta-filters-after-content-ctn'],
]);

it('renders the collapse trigger for `FiltersLayout::AboveContentCollapsible`', function (): void {
    expect(renderPositionedTableFilters(FiltersLayout::AboveContentCollapsible))
        ->toContain('x-on:click="areFiltersOpen = ! areFiltersOpen"')
        ->toContain('x-show="areFiltersOpen"');

    expect(renderPositionedTableFilters(FiltersLayout::AboveContent))
        ->not->toContain('x-on:click="areFiltersOpen = ! areFiltersOpen"');
});

it('renders nothing in the positioned parts for dialog and hidden layouts', function (FiltersLayout $filtersLayout) use ($positionContainerClasses): void {
    $html = renderPositionedTableFilters($filtersLayout);

    foreach ($positionContainerClasses as $containerClass) {
        expect($html)->not->toContain($containerClass);
    }
})->with([
    'dropdown' => FiltersLayout::Dropdown,
    'modal' => FiltersLayout::Modal,
    'hidden' => FiltersLayout::Hidden,
]);

it('renders the plain filters form in a custom layout regardless of the `FiltersLayout`', function () use ($positionContainerClasses): void {
    $html = livewireTableWithParts(
        [TableFilters::make()],
        fn (Table $table): Table => $table->filtersLayout(FiltersLayout::Hidden),
    )->html();

    expect($html)->toContain('class="fi-ta-filters"');

    foreach ($positionContainerClasses as $containerClass) {
        expect($html)->not->toContain($containerClass);
    }
});

it('renders the collapse trigger with `collapsible()` in a custom layout', function (): void {
    $html = livewireTableWithParts([TableFilters::make()->collapsible()])->html();

    expect($html)
        ->toContain('fi-ta-filters-above-content-ctn')
        ->toContain('x-on:click="areFiltersOpen = ! areFiltersOpen"');
});
