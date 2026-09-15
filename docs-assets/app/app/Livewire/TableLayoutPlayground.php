<?php

namespace App\Livewire;

use Filament\Actions\CreateAction;
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
use Filament\Tables\Components\TableEmptyState;
use Filament\Tables\Components\TableFilterIndicators;
use Filament\Tables\Components\TableFiltersTrigger;
use Filament\Tables\Components\TableGroup;
use Filament\Tables\Components\TableGroupingSettings;
use Filament\Tables\Components\TableHeader;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TablePaginationLinks;
use Filament\Tables\Components\TablePaginationOverview;
use Filament\Tables\Components\TablePaginationRecordsPerPage;
use Filament\Tables\Components\TableReorderTrigger;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSelectionIndicator;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Livewire\Attributes\Url;

/**
 * Scratch space for trying table layouts: edit `table()` below, drop CSS into the
 * view's `<style>` block, reload `/table-playground?style=table|grid`.
 */
class TableLayoutPlayground extends TablesDemo
{
    #[Url]
    public string $style = 'table';

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
            ->reorderable('phone')
            ->layout(fn (Schema $schema): Schema => $schema->components([
                TableToolbar::make([
                    TablePageCheckbox::make(),
                    TableHeader::make(),
                    TableToolbarActions::make(),
                    TableReorderTrigger::make(),
                    TableGroupingSettings::make(),
                    TableSortingSettings::make(),
                    TableGroup::make([
                        TableSearch::make(),
                        TableFiltersTrigger::make(),
                        TableColumnManager::make(),
                        TablePaginationRecordsPerPage::make(),
                    ]),
                ]),
                TableSelectionIndicator::make(),
                TableFilterIndicators::make(),
                TableContent::make(),
                TableEmptyState::make(),
                TablePagination::make([
                    TablePaginationOverview::make(),
                    TablePaginationLinks::make(),
                ]),
            ]));

        return match ($this->style) {
            'grid' => $this->gridStyle($table),
            default => $this->tableStyle($table),
        };
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
