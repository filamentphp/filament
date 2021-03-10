<?php

namespace Filament\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class ServingFilament
{
    use Dispatchable;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
