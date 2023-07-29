<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GoogleFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $family, array $weights, ?string $url = null): Htmlable
    {
        $family = urlencode($family);
        $weights = implode(';', array_values($weights));
        $url ??= "https://fonts.googleapis.com/css2?family={$family}:wght@{$weights}&display=swap";

        return new HtmlString("
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
