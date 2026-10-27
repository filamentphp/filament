<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class TableRenderHooksBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    public function updatingPaginators(): void
    {
        \Amp\delay(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->loadingSkeleton()
            ->columns([
                Tables\Columns\TextColumn::make('title'),
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
                EmbeddedTable::make(),
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
