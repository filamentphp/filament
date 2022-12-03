<?php

use Filament\FontProviders\Contracts;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SpatieGoogleFontProvider implements Contracts\FontProvider
{
    public function getHtml(?string $url, string $name): Htmlable
    {
        return new HtmlString(Blade::render('@googlefonts'));
    }
}
