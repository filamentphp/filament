<?php

use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithConfigurableFilterPanels as Fixture;
use Filament\Tests\Fixtures\Livewire\PostsTableWithFilterPanels;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Fixture::$configureUsing = null;
});

it('renders each panel in its location and both drive the table', function (): void {
    $posts = Post::factory()->count(10)->create();

    // `is_published` is in the AboveContent panel; `author` in the Dropdown panel.
    livewire(PostsTableWithFilterPanels::class)
        ->assertSeeHtml('fi-ta-filters-above-content-ctn')
        ->assertSeeHtml('fi-ta-filters-dropdown')
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('renders a flat table in the dropdown only, not above content', function (): void {
    Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->assertDontSeeHtml('fi-ta-filters-above-content-ctn')
        ->assertSeeHtml('fi-ta-filters-dropdown');
});

it('applies a panel\'s own `width()` to the rendered side panel', function (): void {
    // The side panel is registered second, so a width taken from the first panel would be wrong.
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('is_published')]),
        FilterPanel::make(FiltersLayout::BeforeContent, [Filter::make('is_recent')])
            ->width(Width::Medium),
    ]);

    expect(livewire(Fixture::class)->html())
        ->toContain('fi-ta-filters-before-content-ctn lg:fi-open fi-width-md');

    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('is_published')]),
        FilterPanel::make(FiltersLayout::BeforeContent, [Filter::make('is_recent')]),
    ]);

    expect(livewire(Fixture::class)->html())
        ->toContain('fi-ta-filters-before-content-ctn lg:fi-open fi-width-xs');
});

it('applies a panel\'s `maxHeight()` to the rendered dropdown', function (): void {
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('is_published')])
            ->maxHeight('400px'),
    ]);

    expect(livewire(Fixture::class)->html())->toContain('max-height: 400px;');
});

it('applies a panel\'s `triggerAction()` to the rendered trigger', function (): void {
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('is_published')])
            ->triggerAction(fn (Action $action): Action => $action->label('Narrow these down')),
    ]);

    expect(livewire(Fixture::class)->html())->toContain('Narrow these down');
});

it('renders a panel\'s reset action in the position the panel asks for', function (): void {
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('is_published')])
            ->resetActionPosition(FiltersResetActionPosition::Footer),
    ]);

    $footerHtml = livewire(Fixture::class)->html();

    // In the footer the reset renders as a button, in the header as a link.
    expect($footerHtml)->toMatch('/<button[^>]*\bfi-btn\b[^>]*wire:click="resetTableFiltersForm/');
    expect($footerHtml)->not->toMatch('/<button[^>]*\bfi-link\b[^>]*wire:click="resetTableFiltersForm/');

    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('is_published')])
            ->resetActionPosition(FiltersResetActionPosition::Header),
    ]);

    $headerHtml = livewire(Fixture::class)->html();

    expect($headerHtml)->toMatch('/<button[^>]*\bfi-link\b[^>]*wire:click="resetTableFiltersForm/');
    expect($headerHtml)->not->toMatch('/<button[^>]*\bfi-btn\b[^>]*wire:click="resetTableFiltersForm/');
});

it('opens a non-collapsible side panel from the filter trigger on mobile only', function (): void {
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::BeforeContent, [Filter::make('is_published')]),
    ]);

    $html = livewire(Fixture::class)->html();

    // The panel is laid out beside the table from `lg` up...
    expect($html)->toContain('fi-ta-filters-before-content-ctn lg:fi-open');
    // ...so its trigger is only needed below that breakpoint, where it floats open instead.
    expect($html)->toContain('fi-ta-filters-trigger-action-ctn lg:fi-hidden');
});

it('keeps the filter trigger at every breakpoint for a collapsible side panel', function (): void {
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::BeforeContentCollapsible, [Filter::make('is_published')]),
    ]);

    $html = livewire(Fixture::class)->html();

    expect($html)->not->toContain('fi-ta-filters-before-content-ctn lg:fi-open');
    expect($html)->toContain('class="fi-ta-filters-trigger-action-ctn"');
});

it('resolves collapsibility from the relevant panel rather than the whole table', function (): void {
    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('is_published')]),
        FilterPanel::make(FiltersLayout::BeforeContentCollapsible, [Filter::make('is_recent')]),
    ]);

    $html = livewire(Fixture::class)->html();

    // The above-content panel is always visible, so a collapsible side panel alongside it
    // must not make it collapse too.
    expect($html)->toContain('fi-ta-filters-above-content-ctn');
    expect($html)->not->toContain('x-show="areFiltersOpen"');

    Fixture::$configureUsing = fn (Table $table): Table => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContentCollapsible, [Filter::make('is_published')]),
    ]);

    expect(livewire(Fixture::class)->html())->toContain('x-show="areFiltersOpen"');
});
