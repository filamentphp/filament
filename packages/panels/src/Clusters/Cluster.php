<?php

namespace Filament\Clusters;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Support\Arr;

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
        return parent::shouldRegisterNavigation() && static::canAccessClusteredComponents();
    }

    public function mount(): void
    {
        foreach ($this->getCachedSubNavigation() as $navigationGroup) {
            foreach ($navigationGroup->getItems() as $navigationItem) {
                redirect($navigationItem->getUrl());

                return;
            }
        }
    }

    public function getSubNavigation(): array
    {
        return $this->generateNavigationItems(static::getClusteredComponents());
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
            ->ucwords();
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return static::$clusterBreadcrumb ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }

    public static function prependClusterSlug(Panel $panel, string $slug): string
    {
        return static::getSlug($panel) . "/{$slug}";
    }

    public static function prependClusterRouteBaseName(Panel $panel, string $name): string
    {
        return (string) str(static::getSlug($panel))
            ->replace('/', '.')
            ->append(".{$name}");
    }

    public static function getSlug(?Panel $panel = null): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return (string) str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->slug();
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        return $panel->generateRouteName(static::getRelativeRouteName($panel));
    }

    public static function getNavigationItemActiveRoutePattern(): string
    {
        return static::getRouteName() . '.*';
    }

    public static function registerRoutes(Panel $panel): void
    {
        static::routes($panel);
    }
}
