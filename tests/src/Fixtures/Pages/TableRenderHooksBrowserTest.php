<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class TableRenderHooksBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable {
        resetTableColumnSearch as resetTableColumnSearchWithoutDelay;
        resetTableSearch as resetTableSearchWithoutDelay;
    }

    protected static bool $shouldRegisterNavigation = false;

    public string $activeTab = 'all';

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->modifyQueryUsing(function (Builder $query): void {
                if (filled($this->tableSearch) || filled($this->tableColumnSearches['title'] ?? null)) {
                    usleep(1_000_000);
                }

                if ($this->activeTab === 'published') {
                    usleep(1_000_000);

                    $query->where('is_published', true);
                }
            })
            ->loadingSkeleton()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(isIndividual: true),
                Tables\Columns\CheckboxColumn::make('published_checkbox')
                    ->state(static fn (Post $record): bool => $record->is_published),
                Tables\Columns\ToggleColumn::make('published_toggle')
                    ->state(static fn (Post $record): bool => $record->is_published),
            ])
            ->recordActions([
                Action::make('view'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Post status')
                    ->livewireProperty('activeTab')
                    ->tabs([
                        'all' => Tab::make('All'),
                        'published' => Tab::make('Published'),
                    ]),
                EmbeddedTable::make(),
            ]);
    }

    public function resetTableColumnSearch(string $column): void
    {
        $this->resetTableColumnSearchWithoutDelay($column);

        usleep(1_000_000);
    }

    public function resetTableSearch(): void
    {
        $this->resetTableSearchWithoutDelay();

        usleep(1_000_000);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createPost')
                ->action(fn () => Post::factory()->create([
                    'title' => 'Created by render hook test',
                ]))
                ->extraAttributes(['data-testid' => 'create-post']),
        ];
    }
}
