<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\BulkAction;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TableQuerySelectionBrowserTest extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    public string $selectedKeys = 'None';

    public string $submittedKeys = '[]';

    public bool $nested = false;

    public bool $currentPageOnly = false;

    public int $pageSize = 2;

    public ?int $selectionLimit = null;

    public function mount(): void
    {
        $this->nested = request()->boolean('nested');
        $this->currentPageOnly = request()->boolean('currentPageOnly');
        $this->pageSize = request()->integer('pageSize', 2);
        $this->selectionLimit = request()->integer('selectionLimit') ?: null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->extraAttributes(['data-testid' => 'selection-table'])
            ->columns([
                TextColumn::make('title')->html(),
                ViewColumn::make('nested')
                    ->view('tables.columns.nested-selection')
                    ->visible($this->nested),
            ])
            ->defaultSort('id')
            ->paginationPageOptions([$this->pageSize])
            ->selectCurrentPageOnly($this->currentPageOnly)
            ->maxSelectableRecords($this->selectionLimit)
            ->selectableRecordsQuery(static fn (Builder $query): Builder => $query->where('is_published', true))
            ->toolbarActions([
                BulkAction::make('inspect')->extraAttributes(['data-testid' => 'inspect-selection'])->action(function (Collection $records): void {
                    $this->selectedKeys = $records->pluck('id')->sort()->implode(',');
                    $this->submittedKeys = json_encode(array_values($this->selectedTableRecords));
                }),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
            Text::make(fn (): string => $this->selectedKeys)->extraAttributes(['data-testid' => 'selected-keys', 'role' => 'status']),
            Text::make(fn (): string => $this->submittedKeys)->extraAttributes(['data-testid' => 'submitted-keys', 'role' => 'status']),
        ]);
    }
}
