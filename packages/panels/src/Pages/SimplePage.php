<?php

namespace Filament\Pages;

use Filament\Support\Enums\MaxWidth;

abstract class SimplePage extends BasePage
{
    protected static string $layout = 'filament-panels::components.layout.simple';

    protected ?string $maxWidth = null;

    protected function getLayoutData(): array
    {
        return [
            'maxWidth' => $this->getMaxWidth(),
        ];
    }

    public function getMaxWidth(): MaxWidth | string | null
    {
        return $this->maxWidth;
    }

    public function hasLogo(): bool
    {
        return true;
    }
}
