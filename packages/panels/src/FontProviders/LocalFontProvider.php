<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;

class LocalFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $family, ?string $url = null): Htmlable
    {
        if (blank($url)) {
            throw new InvalidArgumentException('The local font\'s CSS URL must be specified.');
        }

        return new HtmlString("
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
