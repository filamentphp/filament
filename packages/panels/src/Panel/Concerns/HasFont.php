<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\FontProviders\BunnyFontProvider;
use Filament\FontProviders\LocalFontProvider;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

trait HasFont
{
    protected string | Closure | null $fontFamily = null;

    protected string | Closure | null $fontProvider = null;

    protected string | Closure | null $fontUrl = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $fontPreload = null;

    protected string | Closure | null $monoFontFamily = null;

    protected string | Closure | null $monoFontProvider = null;

    protected string | Closure | null $monoFontUrl = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $monoFontPreload = null;

    protected string | Closure | null $serifFontFamily = null;

    protected string | Closure | null $serifFontProvider = null;

    protected string | Closure | null $serifFontUrl = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $serifFontPreload = null;

    /**
     * @param  array<string> | Closure | null  $preload
     */
    public function font(string | Closure | null $family, string | Closure | null $url = null, string | Closure | null $provider = null, array | Closure | null $preload = null): static
    {
        $this->fontFamily = $family;
        $this->fontUrl = $url;
        $this->fontPreload = $preload;

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

    /**
     * @return array<string>
     */
    public function getFontPreload(): array
    {
        $preload = $this->evaluate($this->fontPreload);

        if ($preload !== null) {
            return $preload;
        }

        if (! $this->hasCustomFontFamily()) {
            return $this->getDefaultFontPreload('inter');
        }

        return [];
    }

    public function getFontPreloadHtml(): Htmlable
    {
        return $this->getPreloadHtml($this->getFontPreload());
    }

    /**
     * @param  array<string> | Closure | null  $preload
     */
    public function monoFont(string | Closure | null $family, string | Closure | null $url = null, string | Closure | null $provider = null, array | Closure | null $preload = null): static
    {
        $this->monoFontFamily = $family;
        $this->monoFontUrl = $url;
        $this->monoFontPreload = $preload;

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

    /**
     * @return array<string>
     */
    public function getMonoFontPreload(): array
    {
        return $this->evaluate($this->monoFontPreload) ?? [];
    }

    public function getMonoFontPreloadHtml(): Htmlable
    {
        return $this->getPreloadHtml($this->getMonoFontPreload());
    }

    /**
     * @param  array<string> | Closure | null  $preload
     */
    public function serifFont(string | Closure | null $family, string | Closure | null $url = null, string | Closure | null $provider = null, array | Closure | null $preload = null): static
    {
        $this->serifFontFamily = $family;
        $this->serifFontUrl = $url;
        $this->serifFontPreload = $preload;

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

    /**
     * @return array<string>
     */
    public function getSerifFontPreload(): array
    {
        return $this->evaluate($this->serifFontPreload) ?? [];
    }

    public function getSerifFontPreloadHtml(): Htmlable
    {
        return $this->getPreloadHtml($this->getSerifFontPreload());
    }

    /**
     * @return array<string>
     */
    protected function getDefaultFontPreload(string $fontId): array
    {
        $fonts = FilamentAsset::getFonts(['filament/filament']);

        foreach ($fonts as $font) {
            if ($font->getId() !== $fontId) {
                continue;
            }

            $path = $font->getPath();

            if (($path === null) || (! is_dir($path))) {
                return [];
            }

            $files = glob($path . '/*-latin-wght-normal-*.woff2');

            if (($files === false) || ($files === [])) {
                return [];
            }

            return [asset($font->getRelativePublicPath() . '/' . basename($files[0]))];
        }

        return [];
    }

    /**
     * @param  array<string>  $urls
     */
    protected function getPreloadHtml(array $urls): Htmlable
    {
        if ($urls === []) {
            return new HtmlString('');
        }

        $html = '';

        foreach ($urls as $url) {
            $html .= "<link rel=\"preload\" href=\"{$url}\" as=\"font\" type=\"font/woff2\" crossorigin />\n";
        }

        return new HtmlString($html);
    }
}
