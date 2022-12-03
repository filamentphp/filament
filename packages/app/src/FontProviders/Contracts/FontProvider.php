<?php

namespace Filament\FontProviders\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface FontProvider
{
    public function getHtml(string $name, ?string $url = null): Htmlable;
}
