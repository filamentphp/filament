<?php

namespace Filament\Schemas\Contracts;

interface HasRenderHookScopes
{
    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array;
}
