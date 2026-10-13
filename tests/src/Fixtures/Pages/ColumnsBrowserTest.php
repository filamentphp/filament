<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Livewire\Attributes\Url;

class ColumnsBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?int $navigationSort = 20;

    protected static bool $shouldRegisterNavigation = false;

    #[Url]
    public bool $relatedRecordLayout = false;

    #[Url]
    public bool $selectColumns = false;

    #[Url]
    public bool $editableColumns = false;

    #[Url]
    public string $saveScenario = 'success';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('allowColumnEdits')
                ->label('Allow column edits')
                ->visible(fn (): bool => $this->editableColumns)
                ->extraAttributes(['data-testid' => 'allow-column-edits'])
                ->action(function (): void {
                    $this->saveScenario = 'success';
                }),
        ];
    }

    public function table(Table $table): Table
    {
        if ($this->editableColumns) {
            $columns = [
                Tables\Columns\CheckboxColumn::make('is_published')->label('Published'),
                Tables\Columns\ToggleColumn::make('json.approved')->label('Approved'),
                Tables\Columns\TextInputColumn::make('title')->label('Title'),
                Tables\Columns\SelectColumn::make('rating')->options([1 => 'Low', 2 => 'High']),
                Tables\Columns\SelectColumn::make('content')->label('Status')->native(false)
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
                Tables\Columns\SelectColumn::make('json.status')->label('Searchable status')->searchableOptions()
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
            ];

            foreach ($columns as $column) {
                $column
                    ->extraAttributes(['data-testid' => 'editable-' . str_replace('.', '-', $column->getName())])
                    ->disabled(fn (): bool => $this->saveScenario === 'disabled')
                    ->rules(fn (): array => $this->saveScenario === 'validation' ? ['in:rejected'] : [])
                    ->beforeStateUpdated(function (Tables\Columns\Column $column): void {
                        if ($this->saveScenario === 'cached-state') {
                            $column->getState();
                        }
                    });

                if (method_exists($column, 'extraInputAttributes')) {
                    $column->extraInputAttributes(['aria-describedby' => 'editable-column-help']);
                }
            }

            if ($this->saveScenario === 'normalize') {
                $columns[2]->updateStateUsing(static function (Post $record, string $state): void {
                    $record->update(['title' => strtoupper($state)]);
                });
            }

            if ($this->saveScenario === 'custom-records') {
                $columns[2]->updateStateUsing(static function (Post $record, string $state): void {
                    Post::query()->whereKey($record->getKey())->update(['title' => strtoupper($state)]);
                });

                return $table->records(static fn () => Post::query()->get())->columns($columns);
            }

            return $table->query(Post::query())->columns($columns);
        }

        if ($this->selectColumns) {
            return $table->query(Post::query())->columns([
                Tables\Columns\SelectColumn::make('rating')
                    ->native(false)
                    ->options([1 => 'Low', 2 => 'Unavailable', 3 => 'High'])
                    ->disableOptionWhen(static fn (string $value): bool => $value === '2')
                    ->extraAttributes(['data-testid' => 'rating-select']),
                Tables\Columns\SelectColumn::make('title')
                    ->label('Publication status')
                    ->searchableOptions()
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->extraAttributes(['data-testid' => 'publication-select']),
            ]);
        }

        if ($this->relatedRecordLayout) {
            return $table->query(Post::query())->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('author.name')
                        ->url(static fn (User $relatedRecord): string => "/authors/{$relatedRecord->getKey()}"),
                    Tables\Columns\TextColumn::make('author.teams.name')
                        ->listWithLineBreaks()
                        ->url(static fn (string $state, Post $record, Team $relatedRecord): string => "/posts/{$record->getKey()}/teams/{$relatedRecord->getKey()}/" . rawurlencode($state)),
                ])->extraAttributes(['data-testid' => 'related-record-layout']),
            ]);
        }

        return $table
            ->query(Post::query())
            ->reorderable('id')
            ->reorderRecordsTriggerAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'toggle-reordering']))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->url(static fn (Post $record): string => "/posts/{$record->getKey()}")
                    ->extraCellAttributes(['data-testid' => 'linked-column']),
                Tables\Columns\TextColumn::make('content')
                    ->label('Content')
                    ->limit(50)
                    ->url('/posts')
                    ->disabledClick()
                    ->extraCellAttributes(['data-testid' => 'disabled-column']),
                Tables\Columns\TextColumn::make('enum_label')
                    ->state(NavigationGroupEnum::Users)
                    ->html()
                    ->extraAttributes(['data-testid' => 'enum-label-column']),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge(),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...($this->editableColumns ? [
                    Text::make('Changes save automatically.')
                        ->extraAttributes(['id' => 'editable-column-help']),
                ] : []),
                EmbeddedTable::make(),
            ]);
    }
}
