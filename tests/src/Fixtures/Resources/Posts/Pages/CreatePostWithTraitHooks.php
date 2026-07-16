<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Fixtures\Resources\Posts\Pages\Concerns\TracksLifecycleHooks;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class CreatePostWithTraitHooks extends CreateRecord
{
    use TracksLifecycleHooks;

    protected static string $resource = PostResource::class;

    /**
     * @var array<string>
     */
    public array $pageHookInvocations = [];

    protected function afterCreate(): void
    {
        $this->pageHookInvocations[] = 'afterCreate';
    }
}
