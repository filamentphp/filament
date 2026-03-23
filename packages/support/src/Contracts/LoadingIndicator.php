<?php

namespace Filament\Support\Contracts;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentAttributeBag;

interface LoadingIndicator extends Htmlable
{
    public function __construct(ComponentAttributeBag $attributes);
}
