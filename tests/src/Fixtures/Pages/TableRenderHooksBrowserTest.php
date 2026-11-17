<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Livewire\Attributes\Url;

class TableRenderHooksBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    #[Url]
    public bool $isTableLazyLoaded = false;

    #[Url]
    public bool $hasCustomLoadingPlaceholder = false;

    #[Url]
    public bool $isSearchOnBlur = false;

    public int $tableRefreshCount = 0;

    public function refreshTable(): void
    {
        $this->tableRefreshCount++;

        $this->forceRender();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->searchOnBlur(fn (): bool => $this->isSearchOnBlur)
            ->lazyLoaded(fn (): bool => $this->isTableLazyLoaded)
            ->loadingPlaceholder(fn () => $this->hasCustomLoadingPlaceholder
                ? view('loading-placeholder', ['label' => 'Preparing table records'])
                : null)
            ->loadingSkeleton()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(fn (): bool => $this->isSearchOnBlur),
                Tables\Columns\CheckboxColumn::make('published_checkbox')
                    ->state(static fn (Post $record): bool => $record->is_published),
                Tables\Columns\ToggleColumn::make('published_toggle')
                    ->state(static fn (Post $record): bool => $record->is_published),
            ])
            ->recordActions([
                Action::make('view')
                    ->extraAttributes(['data-testid' => 'view-record'])
                    ->modalDescription(fn (): string => "Refresh {$this->tableRefreshCount}")
                    ->schema([
                        TextInput::make('note')
                            ->extraInputAttributes(['data-testid' => 'record-note']),
                    ])
                    ->extraModalFooterActions([
                        Action::make('refreshTable')
                            ->action(fn () => $this->refreshTable())
                            ->extraAttributes(['data-testid' => 'refresh-table']),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->isTableLazyLoaded
                    ? Section::make('Lazy table')
                        ->collapsed()
                        ->extraAttributes(['data-testid' => 'lazy-table-section'])
                        ->schema([EmbeddedTable::make()])
                    : EmbeddedTable::make(),
            ]);
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
