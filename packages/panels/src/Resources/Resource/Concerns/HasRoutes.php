<?php

namespace Filament\Resources\Resource\Concerns;

use Closure;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Stringable;

trait HasRoutes
{
    protected static ?string $slug = null;

    protected static ?string $recordRouteKeyName = null;

    /**
     * @var string | array<string>
     */
    protected static string | array $routeMiddleware = [];

    /**
     * @var string | array<string>
     */
    protected static string | array $withoutRouteMiddleware = [];

    public static function resolveRecordRouteBinding(int | string $key, ?Closure $modifyQuery = null): ?Model
    {
        $query = static::getEloquentQuery();

        if ($modifyQuery) {
            $query = $modifyQuery($query) ?? $query;
        }

        return app(static::getModel())
            ->resolveRouteBindingQuery($query, $key, static::getRecordRouteKeyName())
            ->first();
    }

    public static function getRouteBaseName(?string $panel = null): string
    {
        if ($parentResource = static::getParentResourceRegistration()) {
            return $parentResource->getParentResource()::getRouteBaseName($panel) . '.' . $parentResource->getRouteName();
        }

        $panel = $panel ? Filament::getPanel($panel) : Filament::getCurrentOrDefaultPanel();

        $routeBaseName = (string) str(static::getSlug())
            ->replace('/', '.')
            ->prepend('resources.');

        if (filled($cluster = static::getCluster())) {
            $routeBaseName = $cluster::prependClusterRouteBaseName($routeBaseName);
        }

        return $panel->generateRouteName($routeBaseName);
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return static::$recordRouteKeyName;
    }

    public static function routes(Panel $panel, ?Closure $registerPageRoutes = null): void
    {
        Route::name(static::getRelativeRouteName() . '.')
            ->prefix(static::getRoutePrefix())
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->group($registerPageRoutes);
    }

    public static function registerRoutes(Panel $panel, ?Closure $registerPageRoutes = null): void
    {
        $registerPageRoutes ??= function () use ($panel): void {
            foreach (static::getPages() as $name => $page) {
                $page->registerRoute($panel)?->name($name);
            }
        };

        if ($parentResource = static::getParentResourceRegistration()) {
            $parentResource->getParentResource()::registerRoutes($panel, function () use ($parentResource, $registerPageRoutes): void {
                Route::name($parentResource->getRouteName() . '.')
                    ->prefix('{' . $parentResource->getParentRouteParameterName() . '}/' . $parentResource->getSlug())
                    ->group($registerPageRoutes);
            });

            return;
        }

        $registerRoutes = fn () => static::routes($panel, $registerPageRoutes);

        if (filled($cluster = static::getCluster())) {
            Route::name($cluster::prependClusterRouteBaseName('resources.'))
                ->prefix($cluster::prependClusterSlug(''))
                ->group($registerRoutes);

            return;
        }

        Route::name('resources.')->group($registerRoutes);
    }

    public static function getRelativeRouteName(): string
    {
        return (string) str(static::getSlug())->replace('/', '.');
    }

    public static function getRoutePrefix(): string
    {
        return static::getSlug();
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return static::$routeMiddleware;
    }

    /**
     * @return string | array<string>
     */
    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return static::$withoutRouteMiddleware;
    }

    public static function getEmailVerifiedMiddleware(Panel $panel): string
    {
        return $panel->getEmailVerifiedMiddleware();
    }

    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return $panel->getTenantBillingProvider()->getSubscribedMiddleware();
    }

    public static function getSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        $pluralBasenameBeforeResource = (string) str(static::class)
            ->classBasename()
            ->beforeLast('Resource')
            ->pluralStudly();

        $namespacePartBeforeBasename = (string) str(static::class)
            ->beforeLast('\\')
            ->classBasename();

        if ($pluralBasenameBeforeResource === $namespacePartBeforeBasename) {
            return str(static::class)
                ->beforeLast('\\')
                ->whenContains(
                    '\\Resources\\',
                    fn (Stringable $slug): Stringable => $slug->afterLast('\\Resources\\'),
                    fn (Stringable $slug): Stringable => $slug->classBasename(),
                )
                ->explode('\\')
                ->map(fn (string $string) => str($string)->kebab()->slug())
                ->implode('/');
        }

        return str(static::class)
            ->whenContains(
                '\\Resources\\',
                fn (Stringable $slug): Stringable => $slug->afterLast('\\Resources\\'),
                fn (Stringable $slug): Stringable => $slug->classBasename(),
            )
            ->beforeLast('Resource')
            ->pluralStudly()
            ->explode('\\')
            ->map(fn (string $string) => str($string)->kebab()->slug())
            ->implode('/');
    }
}
