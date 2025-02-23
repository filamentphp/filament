<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Livewire\Component;

class PostsTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->groups(fn () => [
                Tables\Grouping\Group::make('author.name')
                    ->label(fn (Table $table, self $livewire) => 'Dynamic label'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->action(fn () => $this->dispatch('title-action-called')),
                Tables\Columns\TextColumn::make('content')
                    ->words(10)
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Action::make('column-action-object')
                            ->action(fn () => $this->dispatch('column-action-object-called')),
                    )
                    ->summarize([
                        Tables\Columns\Summarizers\Range::make('range'),
                        Tables\Columns\Summarizers\Range::make('published_range')
                            ->query(fn (Builder $query) => $query->where('is_published', true)),
                    ]),
                Tables\Columns\TextColumn::make('author.email')
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->summarize([
                        Tables\Columns\Summarizers\Count::make('published_count')
                            ->query(fn (Builder $query) => $query->where('is_published', true)),
                    ]),
                Tables\Columns\TextColumn::make('rating')
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make('average'),
                        Tables\Columns\Summarizers\Average::make('published_average')
                            ->query(fn (Builder $query) => $query->where('is_published', true)),
                        Tables\Columns\Summarizers\Count::make('count'),
                        Tables\Columns\Summarizers\Range::make('range'),
                        Tables\Columns\Summarizers\Range::make('published_range')
                            ->query(fn (Builder $query) => $query->where('is_published', true)),
                        Tables\Columns\Summarizers\Sum::make('sum'),
                        Tables\Columns\Summarizers\Sum::make('published_sum')
                            ->query(fn (Builder $query) => $query->where('is_published', true)),
                    ]),
                Tables\Columns\TextColumn::make('visible'),
                Tables\Columns\TextColumn::make('hidden')
                    ->hidden(),
                Tables\Columns\TextColumn::make('with_state')
                    ->state('correct state'),
                Tables\Columns\TextColumn::make('formatted_state')
                    ->formatStateUsing(fn () => 'formatted state'),
                Tables\Columns\TextColumn::make('json.foo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('json.bar.baz')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('json_array_of_objects.*.value'),
                Tables\Columns\TextColumn::make('author.json.foo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.json.bar.baz')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extra_attributes')
                    ->extraAttributes([
                        'class' => 'text-danger-500',
                    ]),
                Tables\Columns\TextColumn::make('with_description')
                    ->description('description below')
                    ->description('description above', 'above'),
                Tables\Columns\SelectColumn::make('with_options')
                    ->options([
                        'red' => 'Red',
                        'blue' => 'Blue',
                    ]),
                Tables\Columns\TextColumn::make('title2')
                    ->sortable()
                    ->searchable()
                    ->prefix(fn (Post $record): string => $record->is_published ? 'published' : 'unpublished'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_published')
                    ->query(fn (EloquentBuilder $query) => $query->where('is_published', true)),
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->searchable(['name', 'email', 'job']),
                Tables\Filters\SelectFilter::make('select_filter_attribute')
                    ->options([
                        true => 'Published',
                        false => 'Not Published',
                    ])
                    ->attribute('is_published'),
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('hidden')
                    ->hidden(),
            ])
            ->persistFiltersInSession()
            ->headerActions([
                Action::make('data')
                    ->mountUsing(fn (Schema $form) => $form->fill(['foo' => 'bar']))
                    ->schema([
                        TextInput::make('payload')->required(),
                    ])
                    ->action(function (array $data): void {
                        $this->dispatch('data-called', data: $data);
                    }),
                Action::make('arguments')
                    ->requiresConfirmation()
                    ->action(function (array $arguments): void {
                        $this->dispatch('arguments-called', arguments: $arguments);
                    }),
                Action::make('halt')
                    ->requiresConfirmation()
                    ->action(function (Action $action): void {
                        $this->dispatch('halt-called');

                        $action->halt();
                    }),
                Action::make('visible'),
                Action::make('hidden')
                    ->hidden(),
                Action::make('enabled'),
                Action::make('disabled')
                    ->disabled(),
                ActionGroup::make([
                    Action::make('groupedWithVisibleGroupCondition'),
                ])->visible(fn (?Model $record): bool => $record !== null),
                ActionGroup::make([
                    Action::make('groupedWithHiddenGroupCondition'),
                ])->hidden(fn (?Model $record): bool => $record !== null),
                Action::make('hasIcon')
                    ->icon(Heroicon::PencilSquare),
                Action::make('hasLabel')
                    ->label('My Action'),
                Action::make('hasColor')
                    ->color('primary'),
                Action::make('exists'),
                Action::make('existsInOrder'),
                Action::make('url')
                    ->url('https://filamentphp.com'),
                Action::make('urlInNewTab')
                    ->url('https://filamentphp.com', true),
                Action::make('urlNotInNewTab')
                    ->url('https://filamentphp.com'),
                AttachAction::make(),
                AttachAction::make('attachMultiple')
                    ->multiple(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
                ReplicateAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['title'] .= ' (Copy)';

                        return $data;
                    })
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                    ]),
                Action::make('parent')
                    ->schema([
                        TextInput::make('foo')
                            ->required()
                            ->registerActions([
                                Action::make('nested')
                                    ->schema([
                                        TextInput::make('bar')
                                            ->required(),
                                    ])
                                    ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey())),
                                Action::make('cancelParent')
                                    ->schema([
                                        TextInput::make('bar')
                                            ->required(),
                                    ])
                                    ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey()))
                                    ->cancelParentActions(),
                            ]),
                    ])
                    ->action(function (array $data, Post $record): void {
                        $this->dispatch('parent-called', foo: $data['foo'], recordKey: $record->getKey());
                    })
                    ->extraModalFooterActions([
                        Action::make('footer')
                            ->schema([
                                TextInput::make('bar')
                                    ->required(),
                            ])
                            ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey())),
                    ])
                    ->registerModalActions([
                        Action::make('manuallyRegisteredModal')
                            ->schema([
                                TextInput::make('bar')
                                    ->required(),
                            ])
                            ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey())),
                    ]),
                ActionGroup::make([
                    DeleteAction::make('groupedDelete'),
                    ForceDeleteAction::make('groupedForceDelete'),
                    RestoreAction::make('groupedRestore'),
                    Action::make('groupedParent')
                        ->schema([
                            TextInput::make('foo')
                                ->required()
                                ->registerActions([
                                    Action::make('nested')
                                        ->schema([
                                            TextInput::make('bar')
                                                ->required(),
                                        ])
                                        ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey())),
                                    Action::make('cancelParent')
                                        ->schema([
                                            TextInput::make('bar')
                                                ->required(),
                                        ])
                                        ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey()))
                                        ->cancelParentActions(),
                                ]),
                        ])
                        ->action(function (array $data, Post $record): void {
                            $this->dispatch('grouped-parent-called', foo: $data['foo'], recordKey: $record->getKey());
                        })
                        ->extraModalFooterActions([
                            Action::make('footer')
                                ->schema([
                                    TextInput::make('bar')
                                        ->required(),
                                ])
                                ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey())),
                        ])
                        ->registerModalActions([
                            Action::make('manuallyRegisteredModal')
                                ->schema([
                                    TextInput::make('bar')
                                        ->required(),
                                ])
                                ->action(fn (array $data, Post $record) => $this->dispatch('nested-called', bar: $data['bar'], recordKey: $record->getKey())),
                        ]),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                BulkAction::make('data')
                    ->mountUsing(fn (Schema $form) => $form->fill(['foo' => 'bar']))
                    ->schema([
                        TextInput::make('payload')->required(),
                    ])
                    ->action(function (array $data): void {
                        $this->dispatch('data-called', data: $data);
                    }),
                BulkAction::make('arguments')
                    ->requiresConfirmation()
                    ->action(function (array $arguments): void {
                        $this->dispatch('arguments-called', arguments: $arguments);
                    }),
                BulkAction::make('halt')
                    ->requiresConfirmation()
                    ->action(function (BulkAction $action): void {
                        $this->dispatch('halt-called');

                        $action->halt();
                    }),
                BulkAction::make('visible'),
                BulkAction::make('hidden')
                    ->hidden(),
                BulkAction::make('enabled'),
                BulkAction::make('disabled')
                    ->disabled(),
                BulkAction::make('hasIcon')
                    ->icon(Heroicon::PencilSquare),
                BulkAction::make('hasLabel')
                    ->label('My Action'),
                BulkAction::make('hasColor')
                    ->color('primary'),
                BulkAction::make('exists'),
                BulkAction::make('existsInOrder'),
            ])
            ->emptyStateActions([
                Action::make('emptyExists'),
                Action::make('emptyExistsInOrder'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
