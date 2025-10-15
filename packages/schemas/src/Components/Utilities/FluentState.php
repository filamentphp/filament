<?php

namespace Filament\Schemas\Components\Utilities;

use Filament\Schemas\Components\Component;
use Illuminate\Support\Fluent;

class FluentState extends Get
{
    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $path = '', bool $isAbsolute = false): Fluent
    {
        $state = parent::__invoke($path, $isAbsolute);

        if (is_iterable($state)) {
            return new Fluent($state);
        }

        return new Fluent(['state' => $state]);
    }
}
