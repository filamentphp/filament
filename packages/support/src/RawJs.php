<?php

namespace Filament\Support;

use Illuminate\Support\Js;

class RawJs extends Js
{
    public function __construct(string $js)
    {
        $this->js = $js;
    }

    public static function make(string $js): static
    {
        return app(static::class, ['js' => $js]);
    }
}
