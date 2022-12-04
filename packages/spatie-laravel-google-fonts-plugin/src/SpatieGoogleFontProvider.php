<?php

use Filament\FontProviders\Contracts;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SpatieGoogleFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $name, ?string $url = null): Htmlable
    {
        return new HtmlString(Blade::render('@googlefonts'));
    }
}
