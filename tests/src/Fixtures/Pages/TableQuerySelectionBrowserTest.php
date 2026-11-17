<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\BulkAction;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
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

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([TextColumn::make('title')])
            ->defaultSort('id')
            ->paginationPageOptions([2])
            ->selectableRecordsQuery(static fn (Builder $query): Builder => $query->where('is_published', true))
            ->toolbarActions([
                BulkAction::make('inspect')->extraAttributes(['data-testid' => 'inspect-selection'])->action(function (Collection $records): void {
                    $this->selectedKeys = $records->pluck('id')->sort()->implode(',');
                }),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
            Text::make(fn (): string => $this->selectedKeys)->extraAttributes(['data-testid' => 'selected-keys', 'role' => 'status']),
        ]);
    }
}
