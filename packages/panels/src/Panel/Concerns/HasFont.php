<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\FontProviders\BunnyFontProvider;
use Filament\FontProviders\LocalFontProvider;
use Illuminate\Contracts\Support\Htmlable;

trait HasFont
{
    protected string | Closure | null $fontFamily = null;

    protected string | Closure | null $fontProvider = null;

    protected string | Closure | null $fontUrl = null;

    protected string | Closure | null $monoFontFamily = null;

    protected string | Closure | null $monoFontProvider = null;

    protected string | Closure | null $monoFontUrl = null;

    protected string | Closure | null $serifFontFamily = null;

    protected string | Closure | null $serifFontProvider = null;

    protected string | Closure | null $serifFontUrl = null;

    public function font(string | Closure | null $family, string | Closure | null $url = null, string | Closure | null $provider = null): static
    {
        $this->fontFamily = $family;
        $this->fontUrl = $url;

        if (filled($provider)) {
            $this->fontProvider = $provider;
        }

        return $this;
    }

    public function getFontFamily(): string
    {
        return $this->evaluate($this->fontFamily) ?? 'Inter Variable';
    }

    public function hasCustomFontFamily(): bool
    {
        return $this->fontFamily !== null;
    }

    public function getFontHtml(): Htmlable
    {
        return app($this->getFontProvider())->getHtml(
            $this->getFontFamily(),
            $this->getFontUrl(),
        );
    }

    public function getFontProvider(): string
    {
        return $this->evaluate($this->fontProvider) ?? (($this->hasCustomFontFamily()) ? BunnyFontProvider::class : LocalFontProvider::class);
    }

    public function getFontUrl(): ?string
    {
        return $this->evaluate($this->fontUrl);
    }

    public function monoFont(string | Closure | null $family, string | Closure | null $url = null, string | Closure | null $provider = null): static
    {
        $this->monoFontFamily = $family;
        $this->monoFontUrl = $url;

        if (filled($provider)) {
            $this->monoFontProvider = $provider;
        }

        return $this;
    }

    public function getMonoFontFamily(): string
    {
        return $this->evaluate($this->monoFontFamily) ?? 'ui-monospace';
    }

    public function hasCustomMonoFontFamily(): bool
    {
        return $this->monoFontFamily !== null;
    }

    public function getMonoFontHtml(): Htmlable
    {
        return app($this->getMonoFontProvider())->getHtml(
            $this->getMonoFontFamily(),
            $this->getMonoFontUrl(),
        );
    }

    public function getMonoFontProvider(): string
    {
        return $this->evaluate($this->monoFontProvider) ?? (($this->hasCustomMonoFontFamily()) ? BunnyFontProvider::class : LocalFontProvider::class);
    }

    public function getMonoFontUrl(): ?string
    {
        return $this->evaluate($this->monoFontUrl);
    }

    public function serifFont(string | Closure | null $family, string | Closure | null $url = null, string | Closure | null $provider = null): static
    {
        $this->serifFontFamily = $family;
        $this->serifFontUrl = $url;

        if (filled($provider)) {
            $this->serifFontProvider = $provider;
        }

        return $this;
    }

    public function getSerifFontFamily(): string
    {
        return $this->evaluate($this->serifFontFamily) ?? 'ui-serif';
    }

    public function hasCustomSerifFontFamily(): bool
    {
        return $this->serifFontFamily !== null;
    }

    public function getSerifFontHtml(): Htmlable
    {
        return app($this->getSerifFontProvider())->getHtml(
            $this->getSerifFontFamily(),
            $this->getSerifFontUrl(),
        );
    }

    public function getSerifFontProvider(): string
    {
        return $this->evaluate($this->serifFontProvider) ?? (($this->hasCustomSerifFontFamily()) ? BunnyFontProvider::class : LocalFontProvider::class);
    }

    public function getSerifFontUrl(): ?string
    {
        return $this->evaluate($this->serifFontUrl);
    }
}
