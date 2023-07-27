<?php

namespace Filament\Panel\Concerns;

use Filament\FontProviders\BunnyFontProvider;
use Illuminate\Contracts\Support\Htmlable;

trait HasFont
{
    protected ?string $fontFamily = null;

    protected string $fontProvider = BunnyFontProvider::class;

    protected ?string $fontUrl = null;

    protected ?array $fontWeights = null;

    public function font(string $family, ?array $weights = null, ?string $url = null, ?string $provider = null): static
    {
        $this->fontFamily = $family;
        $this->fontWeights = $weights;
        $this->fontUrl = $url;

        if (filled($provider)) {
            $this->fontProvider = $provider;
        }

        return $this;
    }

    public function getFontFamily(): string
    {
        return $this->fontFamily ?? 'Be Vietnam Pro';
    }

    public function getFontHtml(): Htmlable
    {
        return app($this->getFontProvider())->getHtml(
            $this->getFontFamily(),
            $this->getFontWeights(),
            $this->getFontUrl(),
        );
    }

    public function getFontProvider(): string
    {
        return $this->fontProvider;
    }

    public function getFontUrl(): ?string
    {
        return $this->fontUrl;
    }

    public function getFontWeights(): array
    {
        return $this->fontWeights ?? (
            $this->fontFamily
                ? [
                    'normal' => 400,
                    'medium' => 500,
                    'semibold' => 600,
                    'bold' => 700,
                ]
                : [
                    'normal' => 300,
                    'medium' => 400,
                    'semibold' => 500,
                    'bold' => 600,
                ]
        );
    }
}
