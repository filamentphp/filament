<?php

namespace Filament\Tests\Admin\Fixtures\Resources\PostResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function refreshTitle()
    {
        $this->refreshFormData([
            'title',
        ]);
    }
}
