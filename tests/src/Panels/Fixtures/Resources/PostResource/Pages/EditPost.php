<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Models\Post;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
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
                ->action(action: function (Post $record) {
                    DB::afterCommit(function () {
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
