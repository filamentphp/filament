<?php

namespace Filament\Clusters;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Pages\PageConfiguration;
use Filament\Panel;
use Filament\Support\Attributes\ServerOnly;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class Cluster extends Page
{
    protected static ?string $clusterBreadcrumb = null;

    protected static bool $shouldRegisterSubNavigation = true;

    /**
     * @return array<class-string>
     */
    #[ServerOnly]
    public static function getClusteredComponents(): array
    {
        return Filament::getClusteredComponents(static::class);
    }

    #[ServerOnly]
    public static function canAccessClusteredComponents(): bool
    {
        foreach (static::getClusteredComponents() as $component) {
            if ($component::canAccess()) {
                return true;
            }
        }

        return false;
    }

    #[ServerOnly]
    public static function shouldRegisterNavigation(): bool
    {
        return parent::shouldRegisterNavigation() && static::canAccessClusteredComponents();
    }

    #[ServerOnly]
    public function mount(): void
    {
        foreach ($this->getCachedSubNavigation() as $navigationGroup) {
            foreach ($navigationGroup->getItems() as $navigationItem) {
                redirect($navigationItem->getUrl());

                return;
            }
        }
    }

    #[ServerOnly]
    public function getSubNavigation(): array
    {
        return $this->generateNavigationItems(static::getClusteredComponents());
    }

    #[ServerOnly]
    public static function shouldRegisterSubNavigation(): bool
    {
        return static::$shouldRegisterSubNavigation;
    }

    /**
     * @param  array<string, string>  $breadcrumbs
     * @return array<string, string>
     */
    #[ServerOnly]
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

    #[ServerOnly]
    public static function getNavigationLabel(): string | Htmlable
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }

    #[ServerOnly]
    public static function getClusterBreadcrumb(): ?string
    {
        return static::$clusterBreadcrumb ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }

    #[ServerOnly]
    public static function prependClusterSlug(Panel $panel, string $slug): string
    {
        return static::getSlug($panel) . "/{$slug}";
    }

    #[ServerOnly]
    public static function prependClusterRouteBaseName(Panel $panel, string $name): string
    {
        return (string) str(static::getSlug($panel))
            ->replace('/', '.')
            ->append(".{$name}");
    }

    #[ServerOnly]
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

    #[ServerOnly]
    public static function getRouteName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        return $panel->generateRouteName(static::getRelativeRouteName($panel));
    }

    #[ServerOnly]
    public static function getNavigationItemActiveRoutePattern(): string
    {
        return static::getRouteName() . '.*';
    }

    #[ServerOnly]
    public static function registerRoutes(Panel $panel, ?PageConfiguration $configuration = null): void
    {
        static::routes($panel, $configuration);
    }
}
