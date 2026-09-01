<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make(),
            ]),
            Actions\Action::make('randomize_title')
                ->databaseTransaction()
                ->action(action: function (Post $record): void {
                    DB::afterCommit(function (): void {
                        throw new RuntimeException('This exception, happening after the successful commit of the current transaction, should not trigger a rollback by Filament.');
                    });

                    $record->title = 'Test';
                    $record->save();
                }),
        ];
    }

    public function refreshTitle(): void
    {
        $this->refreshFormData([
            'title',
        ]);
    }
}
