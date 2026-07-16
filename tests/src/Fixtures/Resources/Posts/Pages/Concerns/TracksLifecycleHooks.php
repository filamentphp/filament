<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages\Concerns;

trait TracksLifecycleHooks
{
    /**
     * @var array<string>
     */
    public array $traitHookInvocations = [];

    protected function beforeCreateTracksLifecycleHooks(): void
    {
        $this->traitHookInvocations[] = 'beforeCreateTracksLifecycleHooks';
    }

    protected function afterCreateTracksLifecycleHooks(): void
    {
        $this->traitHookInvocations[] = 'afterCreateTracksLifecycleHooks';
    }

    protected function beforeSaveTracksLifecycleHooks(): void
    {
        $this->traitHookInvocations[] = 'beforeSaveTracksLifecycleHooks';
    }

    protected function afterSaveTracksLifecycleHooks(): void
    {
        $this->traitHookInvocations[] = 'afterSaveTracksLifecycleHooks';
    }
}
