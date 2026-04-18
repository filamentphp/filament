<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePostHeaderAction extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->formId('form'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
