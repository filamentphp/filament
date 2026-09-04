<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages\Concerns;

trait TracksLifecycleHooks
{
    /**
     * @var array<string>
     */
    public array $lifecycleHookInvocations = [];

    protected function beforeCreateTracksLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'beforeCreateTracksLifecycleHooks';
    }

    protected function afterCreateTracksLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'afterCreateTracksLifecycleHooks';
    }
}
