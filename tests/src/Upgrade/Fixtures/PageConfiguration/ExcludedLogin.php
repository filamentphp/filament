<?php

namespace Filament\Tests\Upgrade\Fixtures\PageConfiguration;

use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

abstract class ExcludedLogin extends SimplePage
{
    protected Width | string | null $maxWidth = Width::Small;

    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}
