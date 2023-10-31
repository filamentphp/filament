<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class LocalFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $family, ?string $url = null): Htmlable
    {
        if (blank($url)) {
            return new HtmlString('');
        }

        return new HtmlString("
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
