<?php

namespace Filament\Support\Assets;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Js extends Asset
{
    protected bool $isAsync = false;

    protected bool $isDeferred = true;

    protected bool $isCore = false;

    protected string | Htmlable | null $html = null;

    public function async(bool $condition = true): static
    {
        $this->isAsync = $condition;

        return $this;
    }

    public function defer(bool $condition = true): static
    {
        $this->isDeferred = $condition;

        return $this;
    }

    public function core(bool $condition = true): static
    {
        $this->isCore = $condition;

        return $this;
    }

    public function html(string | Htmlable | null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isDeferred(): bool
    {
        return $this->isDeferred;
    }

    public function isCore(): bool
    {
        return $this->isCore;
    }

    public function getHtml(): Htmlable
    {
        $html = $this->html;

        if (str($html)->contains('<script')) {
            return $html instanceof Htmlable ? $html : new HtmlString($html);
        }

        $html ??= $this->getSrc();

        $async = $this->isAsync() ? 'async' : '';
        $defer = $this->isDeferred() ? 'defer' : '';

        return new HtmlString("
            <script
                src=\"{$html}\"
                {$async}
                {$defer}
            ></script>
        ");
    }

    public function getSrc(): string
    {
        if ($this->isRemote()) {
            return $this->getPath();
        }

        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }

    public function getRelativePublicPath(): string
    {
        return "js/{$this->getPackage()}/{$this->getId()}.js";
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
