<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class PostsTableWithGroupedRecordActions extends ListRecords
{
    protected static string $resource = PostResource::class;

    /**
     * @var array<string, array<int, int>>
     */
    public static array $authorizationChecks = [];

    public static bool $isPublished = false;

    public bool $hasColumnsLayout = false;

    public function table(Table $table): Table
    {
        $column = Tables\Columns\TextColumn::make('title');

        return $table
            ->query(Post::query())
            ->columns([
                $this->hasColumnsLayout ? Tables\Columns\Layout\Stack::make([$column]) : $column,
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('publish')
                        ->visible(fn (): bool => ! static::$isPublished)
                        ->action(fn () => static::$isPublished = true),
                    Action::make('discard')
                        ->visible(fn (): bool => ! static::$isPublished)
                        ->action(fn () => static::$isPublished = true),
                ])
                    ->label('Draft')
                    ->button(),
            ])
            ->recordActions([
                ActionGroup::make([
                    $this->countedAction('edit'),
                    $this->countedAction('publish'),
                    $this->countedAction('archive'),
                ]),
            ]);
    }

    protected function countedAction(string $name): Action
    {
        return Action::make($name)
            ->url(fn (Post $record): string => '/posts/' . $record->getKey())
            ->authorize(function (Post $record) use ($name): bool {
                static::$authorizationChecks[$name][$record->getKey()] = (static::$authorizationChecks[$name][$record->getKey()] ?? 0) + 1;

                return true;
            });
    }
}
