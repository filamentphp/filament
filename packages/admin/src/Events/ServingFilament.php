<?php

namespace Filament\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class ServingFilament
{
    use Dispatchable;

    public function __construct(
        public Request $request,
    ) {}
}
