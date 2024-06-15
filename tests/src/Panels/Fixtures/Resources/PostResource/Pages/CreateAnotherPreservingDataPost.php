<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
use Illuminate\Support\Arr;

class CreateAnotherPreservingDataPost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function preserveFormDataWhenCreatingAnother(array $data): array
    {
        return Arr::only($data, ['tags', 'rating']);
    }
}
