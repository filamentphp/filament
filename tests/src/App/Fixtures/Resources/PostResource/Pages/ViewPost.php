<?php

namespace Filament\Tests\App\Fixtures\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\App\Fixtures\Resources\PostResource;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function refreshTitle()
    {
        $this->refreshFormData([
            'title',
        ]);
    }
}
