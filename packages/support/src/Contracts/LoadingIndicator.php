<?php

namespace Filament\Support\Contracts;

use Illuminate\View\ComponentAttributeBag;

interface LoadingIndicator
{
    public function toHtml(ComponentAttributeBag $attributes): string;
}
