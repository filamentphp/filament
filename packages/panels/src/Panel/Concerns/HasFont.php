<?php

namespace Filament\Panel\Concerns;

use Filament\FontProviders\BunnyFontProvider;
use Illuminate\Contracts\Support\Htmlable;

trait HasFont
{
    protected string $fontFamily = 'Be Vietnam Pro';

    protected string $fontProvider = BunnyFontProvider::class;

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

    public function getFontProvider(): string
    {
        return $this->fontProvider;
    }

    public function getFontHtml(): Htmlable
    {
        return app($this->getFontProvider())->getHtml(
            $this->getFontFamily(),
            $this->getFontUrl(),
        );
    }

    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    public function getFontUrl(): ?string
    {
        return $this->fontUrl;
    }
}
