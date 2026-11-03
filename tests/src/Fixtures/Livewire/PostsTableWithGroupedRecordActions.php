<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostsTableWithGroupedRecordActions extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    /**
     * @var array<string, array<int, int>>
     */
    public static array $authorizationChecks = [];

    public static bool $isPublished = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            // What a resource's `ListRecords` page does for every row: hand the record to a
            // grouped record action and to each group above it, then ask whether it is hidden.
            ->recordUrl(function (Post $record, Table $table): ?string {
                $action = $table->getAction('edit');

                $action->record($record);

                $actionGroup = $action->getGroup();

                while ($actionGroup) {
                    $actionGroup->record($record);

                    $actionGroup = $actionGroup->getGroup();
                }

                $action->isHidden();

                return null;
            })
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

    public function render(): View
    {
        return view('livewire.table');
    }

    protected function countedAction(string $name): Action
    {
        return Action::make($name)
            ->authorize(function (Post $record) use ($name): bool {
                static::$authorizationChecks[$name][$record->getKey()] = (static::$authorizationChecks[$name][$record->getKey()] ?? 0) + 1;

                return true;
            });
    }
}
