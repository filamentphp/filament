<?php

namespace Filament\FontProviders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BunnyFontProvider implements Contracts\FontProvider
{
    public function getHtml(string $name, ?string $url = null): Htmlable
    {
        $name = Str::kebab($name);
        $url ??= "https://fonts.bunny.net/css?family={$name}:400,500,700&display=swap";

        return new HtmlString("
            <link rel=\"preconnect\" href=\"https://fonts.bunny.net\">
            <link href=\"{$url}\" rel=\"stylesheet\" />
        ");
    }
}
