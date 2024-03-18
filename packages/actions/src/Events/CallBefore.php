<?php

namespace Filament\Actions\Events;

use Filament\Actions\Action;
use Illuminate\Foundation\Events\Dispatchable;

class CallBefore
{
    use Dispatchable;

    public function __construct(
        protected Action $action,
    ) {
    }
}
