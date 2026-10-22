<?php

namespace Filament\Pages;

use Filament\Support\Attributes\ServerOnly;

abstract class SimplePage extends BasePage
{
    use Concerns\HasMaxWidth;
    use Concerns\HasTopbar;

    protected string $view = 'filament-panels::pages.simple';

    protected static string $layout = 'filament-panels::components.layout.simple';

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => $this->hasTopbar(),
            'maxContentWidth' => $maxContentWidth = $this->getMaxWidth() ?? $this->getMaxContentWidth(),
            'maxWidth' => $maxContentWidth,
        ];
    }

    #[ServerOnly]
    public function hasLogo(): bool
    {
        return true;
    }
}
