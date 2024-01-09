<?php

namespace Filament\Clusters;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class Cluster extends Page
{
    protected static ?string $clusterBreadcrumb = null;

    /**
     * @return array<class-string>
     */
    public static function getClusteredComponents(): array
    {
        return Filament::getClusteredComponents(static::class);
    }

    public static function canAccessClusteredComponents(): bool
    {
        foreach (static::getClusteredComponents() as $component) {
            if ($component::canAccess()) {
                return true;
            }
        }

        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessClusteredComponents();
    }

    public function mount(): void
    {
        foreach (static::getClusteredComponents() as $component) {
            if (! $component::canAccess()) {
                continue;
            }

            redirect($component::getUrl());
        }
    }

    /**
     * @param  array<string, string>  $breadcrumbs
     * @return array<string, string>
     */
    public static function unshiftClusterBreadcrumbs(array $breadcrumbs): array
    {
        $clusterBreadcrumb = static::getClusterBreadcrumb();

        if (Arr::first($breadcrumbs) === $clusterBreadcrumb) {
            return $breadcrumbs;
        }

        return [
            ...[static::getUrl() => $clusterBreadcrumb],
            ...$breadcrumbs,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return static::$clusterBreadcrumb ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function prependClusterSlug(string $slug): string
    {
        return static::getSlug() . "/{$slug}";
    }

    public static function prependClusterRouteBaseName(string $name): string
    {
        return (string) str(static::getSlug())
            ->replace('/', '.')
            ->append(".{$name}");
    }

    public static function getSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return (string) str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->slug();
    }

    public static function getRouteName(?string $panel = null): string
    {
        $panel = $panel ? Filament::getPanel($panel) : Filament::getCurrentPanel();

        return $panel->generateRouteName((string) str(static::getSlug())
            ->replace('/', '.'));
    }

    public static function routes(Panel $panel): void
    {
        $slug = static::getSlug();
        $routeName = (string) str($slug)
            ->replace('/', '.');

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name($routeName);
    }

    public static function getNavigationItemActiveRoutePattern(): string
    {
        return static::getRouteName() . '.*';
    }
}
