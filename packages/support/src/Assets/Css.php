<?php

namespace Filament\Support\Assets;

use Closure;
use Composer\InstalledVersions;
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

        $href = '/css/filament/';

        $package = $this->getPackage();

        if (filled($package)) {
            $href .= "{$package}/";
        }

        $href .= "{$this->getName()}.css";

        return asset($href) . '?v=' . InstalledVersions::getVersion('filament/support');
    }

    public function getHtml(): Htmlable
    {
        $html = value($this->html);

        if (str($html)->contains('<link')) {
            return $html instanceof Htmlable ? $html : new HtmlString($html);
        }

        $html ??= $this->getHref();

        return new HtmlString("<link href=\"{$html}\" rel=\"stylesheet\" />");
    }

    public function getPublicPath(): string
    {
        $path = '';

        $package = $this->getPackage();

        if (filled($package)) {
            $path .= $package;
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= "{$this->getName()}.css";

        return public_path("css/filament/{$path}");
    }
}
