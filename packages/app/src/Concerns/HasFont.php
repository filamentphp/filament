<?php

namespace Filament\Concerns;

use Filament\FontProviders\BunnyFontProvider;
use Illuminate\Contracts\Support\Htmlable;

trait HasFont
{
    protected string $fontName = 'Be Vietnam Pro';

    protected string $fontProvider = BunnyFontProvider::class;

    protected ?string $fontUrl = null;

    public function font(string $name, ?string $url = null, ?string $provider = null): static
    {
        $this->fontName = $name;
        $this->fontUrl = $url;

        if (filled($provider)) {
            $this->fontProvider = $provider;
        }

        return $this;
    }

    public function getFontProvider(): string
    {
        return $this->fontProvider;
    }

    public function getFontHtml(): Htmlable
    {
        return app($this->getFontProvider())->getHtml(
            $this->getFontName(),
            $this->getFontUrl(),
        );
    }

    public function getFontName(): string
    {
        return $this->fontName;
    }

    public function getFontUrl(): ?string
    {
        return $this->fontUrl;
    }
}