<?php

namespace Filament\Schema\Components\StateCasts\Contracts;

interface StateCast
{
    public function get(mixed $state): mixed;

    public function set(mixed $state): mixed;
}
