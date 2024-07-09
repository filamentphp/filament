<?php

namespace Filament\Panel\Concerns;

use Filament\FontProviders\BunnyFontProvider;
use Filament\FontProviders\LocalFontProvider;
use Illuminate\Contracts\Support\Htmlable;

trait HasFont
{
    protected ?string $fontFamily = null;

    protected ?string $fontProvider = null;

    protected ?string $fontUrl = null;

    protected ?string $monoFontFamily = null;

    protected ?string $monoFontProvider = null;

    protected ?string $monoFontUrl = null;

    protected ?string $serifFontFamily = null;

    protected ?string $serifFontProvider = null;

    protected ?string $serifFontUrl = null;

    public function font(string $family, ?string $url = null, ?string $provider = null): static
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
        return $this->fontFamily ?? 'Inter Variable';
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
        return $this->fontProvider ?? (($this->hasCustomFontFamily()) ? BunnyFontProvider::class : LocalFontProvider::class);
    }

    public function getFontUrl(): ?string
    {
        return $this->fontUrl;
    }

    public function monoFont(string $family, ?string $url = null, ?string $provider = null): static
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
        return $this->monoFontFamily ?? 'ui-monospace';
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
        return $this->monoFontProvider ?? (($this->hasCustomMonoFontFamily()) ? BunnyFontProvider::class : LocalFontProvider::class);
    }

    public function getMonoFontUrl(): ?string
    {
        return $this->monoFontUrl;
    }

    public function serifFont(string $family, ?string $url = null, ?string $provider = null): static
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
        return $this->serifFontFamily ?? 'ui-serif';
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
        return $this->serifFontProvider ?? (($this->hasCustomSerifFontFamily()) ? BunnyFontProvider::class : LocalFontProvider::class);
    }

    public function getSerifFontUrl(): ?string
    {
        return $this->serifFontUrl;
    }
}
