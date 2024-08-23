<?php

namespace Filament\Support\Assets;

use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Js extends Asset
{
    protected bool $isAsync = false;

    protected bool $isDeferred = false;

    protected bool $isCore = false;

    protected bool $isNavigateOnce = true;

    protected bool $isModule = false;

    protected array $extraAttributes = [];

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

    public function navigateOnce(bool $condition = true): static
    {
        $this->isNavigateOnce = $condition;

        return $this;
    }

    public function module(bool $condition = true): static
    {
        $this->isModule = $condition;

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

    public function isNavigateOnce(): bool
    {
        return $this->isNavigateOnce;
    }

    public function isModule(): bool
    {
        return $this->isModule;
    }

    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
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
        $module = $this->isModule() ? 'type="module"' : '';
        $extraAttributes = $this->getExtraAttributes();

        $hasSpaMode = FilamentView::hasSpaMode();

        $navigateOnce = ($hasSpaMode && $this->isNavigateOnce()) ? 'data-navigate-once' : '';
        $navigateTrack = $hasSpaMode ? 'data-navigate-track' : '';

        return new HtmlString(
            "
            <script
                src=\"{$html}\"
                {$async}
                {$defer}
                {$module}
                {$extraAttributes}
                {$navigateOnce}
                {$navigateTrack}
            ></script>
        ",
        );
    }

    public function getExtraAttributes(): string
    {
        $attributes = '';

        foreach ($this->extraAttributes as $key => $value) {
            $attributes .= " {$key}=\"{$value}\"";
        }

        return $attributes;
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
        $path = config('filament.assets_path', '');

        return ltrim("{$path}/js/{$this->getPackage()}/{$this->getId()}.js", '/');
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
