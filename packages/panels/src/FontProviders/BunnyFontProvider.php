<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BunnyFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $family, array $weights, ?string $url = null): Htmlable
    {
        $family = Str::kebab($family);
        $weights = implode(',', array_values($weights));
        $url ??= "https://fonts.bunny.net/css?family={$family}:{$weights}&display=swap";

        return new HtmlString("
            <link rel=\"preconnect\" href=\"https://fonts.bunny.net\">
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
