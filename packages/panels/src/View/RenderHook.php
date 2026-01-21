<?php

namespace Filament\View;

use Illuminate\Contracts\View\View;

abstract class RenderHook
{
    /**
     * @return non-empty-array<string>|null
     */
    public static function getScopes(): ?array
    {
        return null;
    }

    /**
     * @return string | array<string>
     */
    abstract public static function getRenderHooks(): string | array;

    /**
     * @param array<string> $scopes
     * @param array<mixed> $data
     */
    abstract public function render(array $scopes, array $data): View | string;
}
