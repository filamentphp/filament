<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Commands\CacheComponentsCommand;

trait CanCachePanelComponents
{
    protected function canCachePanelComponents(): bool
    {
        return class_exists(CacheComponentsCommand::class);
    }
}
