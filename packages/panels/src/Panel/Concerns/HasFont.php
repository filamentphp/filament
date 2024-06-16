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
}
