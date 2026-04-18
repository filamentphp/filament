<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;

class EditPostSaveInHeader extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
