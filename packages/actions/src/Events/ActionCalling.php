<?php

namespace Filament\Actions\Events;

use Filament\Actions\Action;
use Illuminate\Foundation\Events\Dispatchable;

class ActionCalling
{
    use Dispatchable;

    public function __construct(
        protected Action $action,
    ) {
    }
}
