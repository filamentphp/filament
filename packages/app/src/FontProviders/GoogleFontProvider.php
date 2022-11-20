<?php

namespace Filament\FontProviders;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class GoogleFontProvider implements Contracts\FontProvider
{
    public function getHtml(?string $url): Htmlable
    {
        return new HtmlString("
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
