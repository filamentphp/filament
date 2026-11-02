<?php

namespace Filament\Tests\Upgrade\Fixtures\PageConfiguration;

use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

abstract class ExcludedWidthReader extends SimplePage
{
    public function configuredWidth(): Width | string | null
    {
        return $this->getMaxWidth();
    }
}
