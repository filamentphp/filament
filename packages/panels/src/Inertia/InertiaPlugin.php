<?php

namespace Filament\Inertia;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\HandleInertiaRequests;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Inertia\Middleware;
use LogicException;

class InertiaPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $renderer = null;

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

        return $this;
    }

    public function getRenderer(): string
    {
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
        if (! class_exists(Middleware::class)) {
            throw new LogicException('Install `inertiajs/inertia-laravel` to enable Inertia pages.');
        }

        $panel->middleware([HandleInertiaRequests::class . ':' . $this->middleware]);
    }

    public function boot(Panel $panel): void {}
}
