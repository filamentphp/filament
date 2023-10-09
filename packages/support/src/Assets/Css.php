<?php

namespace Filament\Support\Assets;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Css extends Asset
{
    protected string | Htmlable | Closure | null $html = null;

    public function html(string | Htmlable | Closure | null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function getHref(): string
    {
        if ($this->isRemote()) {
            return $this->getPath();
        }

        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }

    public function getHtml(): Htmlable
    {
        $html = value($this->html);

        if (str($html)->contains('<link')) {
            return $html instanceof Htmlable ? $html : new HtmlString($html);
        }

        $html ??= $this->getHref();

        return new HtmlString("<link
            href=\"{$html}\"
            rel=\"stylesheet\"
            data-navigate-track
        />");
    }

    public function getRelativePublicPath(): string
    {
        $path = config('filament.assets_path', '');

        return ltrim("{$path}/css/{$this->getPackage()}/{$this->getId()}.css", '/');
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
