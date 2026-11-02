<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Enums\Width;

/**
 * @deprecated Configure `BasePage::$maxContentWidth` or override `getMaxContentWidth()` instead.
 */
trait HasMaxWidth
{
    /**
     * @deprecated Use `$maxContentWidth` instead.
     */
    protected Width | string | null $maxWidth = null;

    /**
     * @deprecated Use `getMaxContentWidth()` instead.
     */
    #[Unexposed]
    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}
