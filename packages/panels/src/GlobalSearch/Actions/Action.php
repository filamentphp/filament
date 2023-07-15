<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\StaticAction;

class Action extends StaticAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::LINK_VIEW);

        $this->defaultSize('sm');
    }
}
