<?php

namespace Filament\Actions\Events;

use Filament\Actions\MountableAction;
use Illuminate\Foundation\Events\Dispatchable;

class ActionCalling
{
    use Dispatchable;

    public function __construct(
        protected MountableAction $action,
    ) {}

    public function getAction(): MountableAction
    {
        return $this->action;
    }
}
