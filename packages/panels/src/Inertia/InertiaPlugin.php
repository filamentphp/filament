<?php

namespace Filament\Inertia;

use Closure;
use Composer\InstalledVersions;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\HandleInertiaRequests;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Foundation\Vite;
use Inertia\Middleware;
use LogicException;

class InertiaPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $renderer = null;

    protected ?string $rendererEntry = null;

    /** @var class-string<Middleware> */
    protected string $middleware = Middleware::class;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static */
        return Filament::getCurrentPanel()->getPlugin('inertia');
    }

    public function getId(): string
    {
        return 'inertia';
    }

    public function renderer(string | Closure | null $renderer): static
    {
        $this->renderer = $renderer;
        $this->rendererEntry = null;

        return $this;
    }

    public function rendererEntry(?string $entry): static
    {
        $this->rendererEntry = $entry;
        $this->renderer = null;

        return $this;
    }

    public function getRendererEntry(): ?string
    {
        return $this->rendererEntry;
    }

    public function getRenderer(): string
    {
        if ($this->rendererEntry !== null) {
            return app(Vite::class)->asset($this->rendererEntry);
        }

        return $this->evaluate($this->renderer)
            ?? throw new LogicException('Configure an Inertia renderer on the panel plugin or override `getInertiaRenderer()` on the page.');
    }

    /** @param class-string<Middleware> $middleware */
    public function middleware(string $middleware): static
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function register(Panel $panel): void
    {
        if (! static::hasCompatibleAdapter()) {
            throw new LogicException('Install `inertiajs/inertia-laravel:^3.3` to enable Inertia pages. Other adapter versions can still be used outside Filament when this plugin is disabled.');
        }

        $panel->middleware([HandleInertiaRequests::class . ':' . $this->middleware]);
    }

    public static function hasCompatibleAdapter(): bool
    {
        if ((! class_exists(Middleware::class)) || (! InstalledVersions::isInstalled('inertiajs/inertia-laravel'))) {
            return false;
        }

        $version = InstalledVersions::getVersion('inertiajs/inertia-laravel');

        return ($version !== null) && version_compare($version, '3.3.0', '>=') && version_compare($version, '4.0.0-dev', '<');
    }

    public function boot(Panel $panel): void {}
}
