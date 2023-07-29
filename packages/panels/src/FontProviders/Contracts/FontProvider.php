<?php

namespace Filament\FontProviders\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface FontProvider
{
    /**
     * @param  array<string, int>  $weights
     */
    public function getHtml(string $family, array $weights, ?string $url = null): Htmlable;
}
