<?php

namespace Filament\FontProviders\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface FontProvider
{
    public function getHtml(string $family, ?string $url = null): Htmlable;
}
