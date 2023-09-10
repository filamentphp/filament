<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\StaticAction;
use Filament\Support\Enums\ActionSize;

class Action extends StaticAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::LINK_VIEW);

        $this->defaultSize(ActionSize::Small);
    }
}
