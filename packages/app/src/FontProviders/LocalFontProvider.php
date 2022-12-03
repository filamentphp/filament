<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class LocalFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $name, ?string $url = null): Htmlable
    {
        return new HtmlString("
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
