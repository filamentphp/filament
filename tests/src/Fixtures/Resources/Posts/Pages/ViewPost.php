<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Illuminate\Database\Eloquent\Model;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\CreateAction::make('createUserWithModal')
                ->modal()
                ->model(User::class)
                ->schema([
                    TextInput::make('name'),
                ]),
            Actions\Action::make('simpleModalAction')
                ->modal()
                ->model(User::class)
                ->schema([
                    TextInput::make('name'),
                ])
                ->action(function (): void {
                    $this->dispatch('simple-modal-action-called');
                }),
            Actions\Action::make('actionWithCustomModel')
                ->modal()
                ->model(User::class)
                ->schema([
                    TextInput::make('name'),
                ])
                ->action(function (?Model $record, Actions\Action $action): void {
                    $this->dispatch('action-with-custom-model-called', [
                        'recordIsNull' => $record === null,
                        'modelIsUser' => $action->getModel() === User::class,
                    ]);
                }),
            Actions\Action::make('actionWithSameModel')
                ->modal()
                ->model(Post::class)
                ->schema([
                    TextInput::make('title'),
                ])
                ->action(function (?Model $record, Actions\Action $action): void {
                    $this->dispatch('action-with-same-model-called', [
                        'recordIsPost' => $record instanceof Post,
                        'modelIsPost' => $action->getModel() === Post::class,
                    ]);
                }),
            Actions\Action::make('actionWithoutCustomModel')
                ->modal()
                ->schema([
                    TextInput::make('title'),
                ])
                ->action(function (?Model $record, Actions\Action $action): void {
                    $this->dispatch('action-without-custom-model-called', [
                        'recordIsPost' => $record instanceof Post,
                        'modelIsPost' => $action->getModel() === Post::class,
                    ]);
                }),
        ];
    }

    public function refreshTitle()
    {
        $this->refreshFormData([
            'title',
        ]);
    }
}
