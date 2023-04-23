<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\Concerns\CanEmitEvent;
use Filament\Actions\StaticAction;

class Action extends StaticAction
{
    use CanEmitEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::LINK_VIEW);

        $this->defaultSize('sm');
    }
}
