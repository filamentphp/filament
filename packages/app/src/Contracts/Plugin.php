<?php

namespace Filament\Contracts;

use Filament\Context;

interface Plugin
{
    public function register(Context $context): void;
}
