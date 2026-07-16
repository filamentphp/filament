<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Fixtures\Resources\Posts\Pages\Concerns\TracksLifecycleHooks;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class EditPostWithTraitHooks extends EditRecord
{
    use TracksLifecycleHooks;

    protected static string $resource = PostResource::class;

    /**
     * @var array<string>
     */
    public array $pageHookInvocations = [];

    protected function afterSave(): void
    {
        $this->pageHookInvocations[] = 'afterSave';
    }
}
