<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Livewire\Component;

class PostsTable extends Component implements HasForms, Tables\Contracts\HasTable
{
    use InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
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
                        Tables\Actions\Action::make('column-action-object')
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
                    ->relationship('author', 'name'),
                Tables\Filters\SelectFilter::make('select_filter_attribute')
                    ->options([
                        true => 'Published',
                        false => 'Not Published',
                    ])
                    ->attribute('is_published'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->persistFiltersInSession()
            ->headerActions([
                Tables\Actions\Action::make('data')
                    ->mountUsing(fn (ComponentContainer $form) => $form->fill(['foo' => 'bar']))
                    ->form([
                        TextInput::make('payload')->required(),
                    ])
                    ->action(function (array $data) {
                        $this->dispatch('data-called', data: $data);
                    }),
                Tables\Actions\Action::make('arguments')
                    ->requiresConfirmation()
                    ->action(function (array $arguments) {
                        $this->dispatch('arguments-called', arguments: $arguments);
                    }),
                Tables\Actions\Action::make('halt')
                    ->requiresConfirmation()
                    ->action(function (Tables\Actions\Action $action) {
                        $this->dispatch('halt-called');

                        $action->halt();
                    }),
                Tables\Actions\Action::make('visible'),
                Tables\Actions\Action::make('hidden')
                    ->hidden(),
                Tables\Actions\Action::make('enabled'),
                Tables\Actions\Action::make('disabled')
                    ->disabled(),
                Tables\Actions\Action::make('hasIcon')
                    ->icon('heroicon-m-pencil-square'),
                Tables\Actions\Action::make('hasLabel')
                    ->label('My Action'),
                Tables\Actions\Action::make('hasColor')
                    ->color('primary'),
                Tables\Actions\Action::make('exists'),
                Tables\Actions\Action::make('existsInOrder'),
                Tables\Actions\Action::make('url')
                    ->url('https://filamentphp.com'),
                Tables\Actions\Action::make('urlInNewTab')
                    ->url('https://filamentphp.com', true),
                Tables\Actions\Action::make('urlNotInNewTab')
                    ->url('https://filamentphp.com'),
                Tables\Actions\AttachAction::make(),
                Tables\Actions\AttachAction::make('attachMultiple')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make('groupedDelete'),
                    Tables\Actions\ForceDeleteAction::make('groupedForceDelete'),
                    Tables\Actions\RestoreAction::make('groupedRestore'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('data')
                    ->mountUsing(fn (ComponentContainer $form) => $form->fill(['foo' => 'bar']))
                    ->form([
                        TextInput::make('payload')->required(),
                    ])
                    ->action(function (array $data) {
                        $this->dispatch('data-called', data: $data);
                    }),
                Tables\Actions\BulkAction::make('arguments')
                    ->requiresConfirmation()
                    ->action(function (array $arguments) {
                        $this->dispatch('arguments-called', arguments: $arguments);
                    }),
                Tables\Actions\BulkAction::make('halt')
                    ->requiresConfirmation()
                    ->action(function (Tables\Actions\BulkAction $action) {
                        $this->dispatch('halt-called');

                        $action->halt();
                    }),
                Tables\Actions\BulkAction::make('visible'),
                Tables\Actions\BulkAction::make('hidden')
                    ->hidden(),
                Tables\Actions\BulkAction::make('enabled'),
                Tables\Actions\BulkAction::make('disabled')
                    ->disabled(),
                Tables\Actions\BulkAction::make('hasIcon')
                    ->icon('heroicon-m-pencil-square'),
                Tables\Actions\BulkAction::make('hasLabel')
                    ->label('My Action'),
                Tables\Actions\BulkAction::make('hasColor')
                    ->color('primary'),
                Tables\Actions\BulkAction::make('exists'),
                Tables\Actions\BulkAction::make('existsInOrder'),
            ])
            ->emptyStateActions([
                Tables\Actions\Action::make('emptyExists'),
                Tables\Actions\Action::make('emptyExistsInOrder'),
            ]);
    }

    public function render(): View
    {
        return view('tables.fixtures.table');
    }
}
