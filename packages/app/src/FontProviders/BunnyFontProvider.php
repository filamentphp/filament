<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class BunnyFontProvider implements Contracts\FontProvider
{
    public function getHtml(?string $url): Htmlable
    {
        return new HtmlString("
            <link rel=\"preconnect\" href=\"https://fonts.bunny.net\">
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
