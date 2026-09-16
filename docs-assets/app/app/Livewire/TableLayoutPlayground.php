<?php

namespace App\Livewire;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Grid as SchemaGrid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableColumnManager;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableContentHeader;
use Filament\Tables\Components\TableEmptyState;
use Filament\Tables\Components\TableFilterIndicators;
use Filament\Tables\Components\TableFilters;
use Filament\Tables\Components\TableFiltersTrigger;
use Filament\Tables\Components\TableGroupingSettings;
use Filament\Tables\Components\TableHeader;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TablePaginationLinks;
use Filament\Tables\Components\TablePaginationRecordsPerPage;
use Filament\Tables\Components\TableReorderTrigger;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSelectionIndicator;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableSplit;
use Filament\Tables\Components\TableStack;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Livewire\Attributes\Url;

/**
 * Scratch space for trying table layouts: edit `table()` below, drop CSS into the
 * view's `<style>` block, reload `/table-playground?style=table|grid&layout=scratch|cards|sidebar|grid-controls`.
 */
class TableLayoutPlayground extends TablesDemo
{
    #[Url]
    public string $style = 'table';

    #[Url]
    public string $layout = 'scratch';

    public function mount(): void {}

    public function table(Table $table): Table
    {
        $table = $this->layoutVariantTable($table)
            ->heading('Team')
            ->description('Everyone with access to this workspace.')
            ->headerActions([
                CreateAction::make(),
            ])
            ->groups([
                Group::make('job')->collapsible(),
            ])
            ->defaultGroup('job')
            ->defaultSort('name')
            ->striped()
            ->reorderableColumns()
            ->extremePaginationLinks()
            ->paginationPageOptions([5, 10, 25])
            // ponytail: users has no sort column; usersTable() reseeds on every render, so reorder writes are harmless
            ->reorderable('phone');

        $table = match ($this->style) {
            'grid' => $this->gridStyle($table),
            default => $this->tableStyle($table),
        };

        return match ($this->layout) {
            'cards' => $this->cardsLayout($table),
            'sidebar' => $this->sidebarLayout($table),
            'grid-controls' => $this->gridControlsLayout($table),
            default => $this->scratchLayout($table),
        };
    }

    /**
     * Free-form experiment: edit and reload.
     */
    protected function scratchLayout(Table $table): Table
    {
        return $table
            // ->contained(false)
            ->layout(fn (Schema $schema): Schema => $schema->components([
                TableToolbar::make([
                    // TODO: Decompose into title, description and actions
                    TableHeader::make(),

                    TableStack::make([
                        TableToolbarActions::make(),
                        TableReorderTrigger::make(),
                        TableGroupingSettings::make(),
                    ]),

                    TableStack::make([
                        TableFiltersTrigger::make(),
                        TableColumnManager::make(),
                        TablePaginationRecordsPerPage::make(),
                    ]),
                ]),
                TableSelectionIndicator::make(),
                TableContentHeader::make([
                    TableSearch::make()->grow(false),
                    TablePageCheckbox::make(),
                    TableFilterIndicators::make(),
                    TableSortingSettings::make()->grow(false),
                ]),
                TableContent::make(),
                TableEmptyState::make(),
                TablePagination::make([
                    TableSplit::make([
                        TablePaginationLinks::make(),
                    ]),
                ]),
            ]));
    }

    /**
     * A frameless list for card grids on content pages: search, sort and filters in one slim row above the cards,
     * so the page heading and the table controls sit at the same level instead of inside a boxed table.
     */
    protected function cardsLayout(Table $table): Table
    {
        return $table
            ->contained(false)
            ->layout(fn (Schema $schema): Schema => $schema->components([
                TableSplit::make([
                    TablePageCheckbox::make()->grow(false),
                    TableToolbarActions::make()->grow(false),
                    TableSearch::make(),
                    TableSortingSettings::make()->grow(false),
                    TableFiltersTrigger::make()->grow(false),
                    TableColumnManager::make()->grow(false),
                ]),
                TableSelectionIndicator::make(),
                TableFilterIndicators::make(),
                TableContent::make(),
                TableEmptyState::make(),
                TablePagination::make(),
            ]));
    }

    /**
     * The filters form always visible in a sidebar next to the records, so filtering never hides behind a dropdown.
     */
    protected function sidebarLayout(Table $table): Table
    {
        return $table
            ->contained(false)
            ->layout(fn (Schema $schema): Schema => $schema->components([
                SchemaGrid::make(['lg' => 4])
                    ->schema([
                        TableStack::make([
                            TableStack::make([
                                TableHeader::make(),
                                TableToolbar::make([
                                    TableToolbarActions::make(),
                                    TableGroupingSettings::make(),
                                    TableStack::make([
                                        TableSearch::make(),
                                        TableColumnManager::make(),
                                    ]),
                                ]),
                                TableSelectionIndicator::make(),
                                TableFilterIndicators::make(),
                                TableContent::make(),
                                TableEmptyState::make(),
                                TablePagination::make(),
                            ])->extraAttributes(['class' => 'fi-ta-main']),
                        ])
                            ->extraAttributes(['class' => 'fi-ta-ctn'])
                            ->columnSpan(['lg' => 3]),
                        Section::make()
                            ->schema([
                                TableFilters::make(),
                            ]),
                    ]),
            ]));
    }

    /**
     * The grey row above a content grid folded into the toolbar: select all, sort and group at the start,
     * search, filters and the column manager at the end. One row less between the heading and the records.
     */
    protected function gridControlsLayout(Table $table): Table
    {
        return $table
            ->layout(fn (Schema $schema): Schema => $schema->components([
                TableHeader::make(),
                TableToolbar::make([
                    TableToolbarActions::make(),
                    TablePageCheckbox::make(),
                    TableSortingSettings::make(),
                    TableGroupingSettings::make(),
                    TableStack::make([
                        TableSearch::make(),
                        TableFiltersTrigger::make(),
                        TableColumnManager::make(),
                    ]),
                ]),
                TableSelectionIndicator::make(),
                TableFilterIndicators::make(),
                TableContent::make(),
                TableEmptyState::make(),
                TablePagination::make(),
            ]));
    }

    protected function tableStyle(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->summarize(Count::make()),
                TextColumn::make('job')
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(isIndividual: true)
                    ->toggleable(),
                TextColumn::make('phone')
                    ->toggleable(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->email_verified_at))
                    ->summarize(Count::make()->query(fn ($query) => $query->whereNotNull('email_verified_at'))),
            ]);
    }

    protected function gridStyle(Table $table): Table
    {
        return $table
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable()
                            ->summarize(Count::make()),
                        TextColumn::make('job')
                            ->sortable(),
                    ]),
                ]),
                Panel::make([
                    TextColumn::make('email')
                        ->icon(Heroicon::Envelope)
                        ->searchable()
                        ->toggleable(),
                    TextColumn::make('phone')
                        ->icon(Heroicon::Phone)
                        ->toggleable(),
                ])->collapsible(),
            ]);
    }

    public function render()
    {
        return view('livewire.table-layout-playground');
    }
}
