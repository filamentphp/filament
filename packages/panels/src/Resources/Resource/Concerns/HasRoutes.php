<?php

namespace Filament\Resources\Resource\Concerns;

use Closure;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function resolveRecordRouteBinding(int | string $key, ?Closure $modifyQuery = null): ?Model
    {
        $query = static::getRecordRouteBindingEloquentQuery();

        if ($modifyQuery) {
            $query = $modifyQuery($query) ?? $query;
        }

        return app(static::getModel())
            ->resolveRouteBindingQuery($query, $key, static::getRecordRouteKeyName())
            ->first();
    }

    public static function getRouteBaseName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        if ($parentResource = static::getParentResourceRegistration()) {
            return $parentResource->getParentResource()::getRouteBaseName($panel) . '.' . $parentResource->getRouteName();
        }

        $routeBaseName = (string) str(static::getSlug($panel))
            ->replace('/', '.')
            ->prepend('resources.');

        if (filled($cluster = static::getCluster())) {
            $routeBaseName = $cluster::prependClusterRouteBaseName($panel, $routeBaseName);
        }

        return $panel->generateRouteName($routeBaseName);
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return static::$recordRouteKeyName;
    }

    public static function routes(Panel $panel, ?Closure $registerPageRoutes = null): void
    {
        Route::name(static::getRelativeRouteName($panel) . '.')
            ->prefix(static::getRoutePrefix($panel))
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
            $parentResource->getParentResource()::registerRoutes($panel, function () use ($panel, $parentResource, $registerPageRoutes): void {
                Route::name($parentResource->getRouteName() . '.')
                    ->prefix('{' . $parentResource->getParentRouteParameterName() . '}/' . static::getSlug($panel))
                    ->group($registerPageRoutes);
            });

            return;
        }

        $registerRoutes = fn () => static::routes($panel, $registerPageRoutes);

        if (filled($cluster = static::getCluster())) {
            Route::name($cluster::prependClusterRouteBaseName($panel, 'resources.'))
                ->prefix($cluster::prependClusterSlug($panel, ''))
                ->group($registerRoutes);

            return;
        }

        Route::name('resources.')->group($registerRoutes);
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return (string) str(static::getSlug($panel))->replace('/', '.');
    }

    public static function getRoutePrefix(Panel $panel): string
    {
        return static::getSlug($panel);
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

    public static function getSlug(?Panel $panel = null): string
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
