<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelManualBoundaries;

use Closure;
use Filament\Clusters\Cluster;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Panel\Concerns\HasNotifications;

class CustomPanel extends Panel
{
    public function passwordResetRouteSlug(string $slug): static
    {
        return parent::passwordResetRouteSlug($slug);
    }
}

class CustomNavigationItem extends NavigationItem
{
    public function activeWhen(?Closure $callback): static
    {
        return parent::activeWhen($callback);
    }
}

trait CustomActivity
{
    public function isChildItemsActive(): bool
    {
        return true;
    }
}

class TraitNavigationItem extends NavigationItem
{
    use CustomActivity;
}

class CustomCluster extends Cluster
{
    public static function unshiftClusterBreadcrumbs(array $breadcrumbs): array
    {
        return parent::unshiftClusterBreadcrumbs($breadcrumbs);
    }
}

class InheritedCluster extends CustomCluster {}

class CanonicalCluster extends Cluster
{
    public static function prependClusterBreadcrumbs(array $breadcrumbs): array
    {
        return $breadcrumbs;
    }
}

class RelativeCluster extends Cluster
{
    public static function breadcrumbs(): array
    {
        self::unshiftClusterBreadcrumbs([]);
        static::unshiftClusterBreadcrumbs([]);

        return parent::unshiftClusterBreadcrumbs([]);
    }
}

class DirectNotifications
{
    use HasNotifications;
}

class PrivateNotifications extends Panel
{
    use HasNotifications {
        databaseNotificationsPollingInterval as private;
    }
}

function manual(CustomPanel $panel, CustomNavigationItem $item, TraitNavigationItem $traitItem, DirectNotifications $notifications, PrivateNotifications $privateNotifications, object $unrelated, mixed $unknown, string $method, string $clusterClass): void
{
    $panel->passwordResetRouteSlug('reset');
    $item->isActiveWhen(null);
    $traitItem->isChildItemsActive();
    $notifications->databaseNotificationsPolling(null);
    $privateNotifications->databaseNotificationsPolling(null);
    $unrelated->passwordResetRequestRouteSlug('email');
    $unrelated->passwordResetRouteSlug('reset');
    $unrelated->databaseNotificationsPolling(null);
    $unrelated->isActiveWhen(null);
    $unrelated->isChildItemsActive();
    $unknown->isActiveWhen(null);
    $panel->{$method}('reset');
    $panel->{'passwordResetRouteSlug'}('reset');
    $clusterClass::unshiftClusterBreadcrumbs([]);
    CustomCluster::unshiftClusterBreadcrumbs([]);
    InheritedCluster::unshiftClusterBreadcrumbs([]);
    CanonicalCluster::unshiftClusterBreadcrumbs([]);
    Cluster::{$method}([]);
    $callback = [$panel, 'passwordResetRouteSlug'];
    $callback = [Cluster::class, 'unshiftClusterBreadcrumbs'];
}

function union(Panel | CustomPanel $panel, NavigationItem | CustomNavigationItem $item): void
{
    $panel->passwordResetRouteSlug('reset');
    $item->isActiveWhen(null);
}
