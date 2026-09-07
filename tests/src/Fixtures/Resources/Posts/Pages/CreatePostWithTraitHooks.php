<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Fixtures\Resources\Posts\Pages\Concerns\TracksLifecycleHooks;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class CreatePostWithTraitHooks extends CreateRecord
{
    use TracksLifecycleHooks;

    protected static string $resource = PostResource::class;

    protected function afterCreate(): void
    {
        $this->lifecycleHookInvocations[] = 'afterCreate';
    }
}
