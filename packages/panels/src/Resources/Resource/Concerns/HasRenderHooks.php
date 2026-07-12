<?php

namespace Filament\Resources\Resource\Concerns;

trait HasRenderHooks
{
    /**
     * @return array<string, callable(): \Illuminate\Contracts\View\View|string>
     */
    public static function getRenderHooks(): array
    {
        return [];
    }
}
