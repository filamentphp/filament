<?php

namespace Filament\Support;

use Illuminate\Support\Js;

class RawJs extends Js
{
    public function __construct(string $js)
    {
        $this->js = $js;
    }
}
