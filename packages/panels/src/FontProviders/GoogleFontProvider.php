<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GoogleFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $family, ?string $url = null): Htmlable
    {
        $family = str_replace(' ', '+', $family);
        $url ??= "https://fonts.googleapis.com/css2?family={$family}:wght@400;500;600;700&display=swap";

        return new HtmlString("
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
