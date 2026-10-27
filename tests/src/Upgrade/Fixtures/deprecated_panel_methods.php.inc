<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelMethods;

use Filament\Clusters\Cluster;
use Filament\Navigation\NavigationItem;
use Filament\Panel;

class SettingsCluster extends Cluster
{
    public static function getClusterBreadcrumb(): string
    {
        return 'Settings';
    }
}

class CustomPanel extends Panel {}

function configure(Panel $panel, CustomPanel $customPanel, ?Panel $nullablePanel, NavigationItem $item, ?NavigationItem $nullableItem, array $arguments): void
{
    $panel->passwordResetRequestRouteSlug(slug: 'send-link')->passwordResetRouteSlug(slug: 'choose-password');
    $panel->passwordResetRoutePrefix(prefix: 'account-recovery');
    $panel->databaseNotificationsPolling(interval: '17s');
    $panel->databaseNotificationsPolling(interval: static fn (): ?string => null);
    $nullablePanel?->passwordResetRequestRouteSlug('request')?->passwordResetRouteSlug('reset')?->databaseNotificationsPolling(null);
    $customPanel->id('admin')->passwordResetRouteSlug(slug: 'reset');
    Panel::make()->passwordResetRequestRouteSlug('email')->passwordResetRouteSlug('password')->databaseNotificationsPolling('31s');
    $item->isActiveWhen(callback: static fn (): bool => true)->isChildItemsActive();
    $nullableItem?->isActiveWhen(callback: null)?->isChildItemsActive();
    NavigationItem::make('Reports')->isActiveWhen(null)->isChildItemsActive();
    $item->ISACTIVEWHEN(callback: null);
    $panel->passwordResetRequestRouteSlug(...$arguments);
    $panel->passwordResetRouteSlug(...$arguments);
    $panel->databaseNotificationsPolling(...$arguments);
    $item->isActiveWhen(...$arguments);
    SettingsCluster::unshiftClusterBreadcrumbs(breadcrumbs: ['/reports' => 'Reports']);
    Cluster::unshiftClusterBreadcrumbs(...$arguments);
    $callback = $panel->passwordResetRequestRouteSlug(...);
    $callback = $panel->passwordResetRouteSlug(...);
    $callback = $panel->databaseNotificationsPolling(...);
    $callback = $item->isActiveWhen(...);
    $callback = $item->isChildItemsActive(...);
    $callback = SettingsCluster::unshiftClusterBreadcrumbs(...);
    $panel->requestPasswordResetRouteSlug('email')->resetPasswordRouteSlug('password')->databaseNotificationsPollingInterval(null);
    $item->activeWhen(null)->hasActiveChildItems();
    SettingsCluster::prependClusterBreadcrumbs([]);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedPanelMethods;

use Filament\Clusters\Cluster;
use Filament\Navigation\NavigationItem;
use Filament\Panel;

class SettingsCluster extends Cluster
{
    public static function getClusterBreadcrumb(): string
    {
        return 'Settings';
    }
}

class CustomPanel extends Panel {}

function configure(Panel $panel, CustomPanel $customPanel, ?Panel $nullablePanel, NavigationItem $item, ?NavigationItem $nullableItem, array $arguments): void
{
    $panel->requestPasswordResetRouteSlug(slug: 'send-link')->resetPasswordRouteSlug(slug: 'choose-password');
    $panel->passwordResetRoutePrefix(prefix: 'account-recovery');
    $panel->databaseNotificationsPollingInterval(interval: '17s');
    $panel->databaseNotificationsPollingInterval(interval: static fn (): ?string => null);
    $nullablePanel?->requestPasswordResetRouteSlug('request')?->resetPasswordRouteSlug('reset')?->databaseNotificationsPollingInterval(null);
    $customPanel->id('admin')->resetPasswordRouteSlug(slug: 'reset');
    Panel::make()->requestPasswordResetRouteSlug('email')->resetPasswordRouteSlug('password')->databaseNotificationsPollingInterval('31s');
    $item->activeWhen(callback: static fn (): bool => true)->hasActiveChildItems();
    $nullableItem?->activeWhen(callback: null)?->hasActiveChildItems();
    NavigationItem::make('Reports')->activeWhen(null)->hasActiveChildItems();
    $item->activeWhen(callback: null);
    $panel->requestPasswordResetRouteSlug(...$arguments);
    $panel->resetPasswordRouteSlug(...$arguments);
    $panel->databaseNotificationsPollingInterval(...$arguments);
    $item->activeWhen(...$arguments);
    SettingsCluster::prependClusterBreadcrumbs(breadcrumbs: ['/reports' => 'Reports']);
    Cluster::prependClusterBreadcrumbs(...$arguments);
    $callback = $panel->requestPasswordResetRouteSlug(...);
    $callback = $panel->resetPasswordRouteSlug(...);
    $callback = $panel->databaseNotificationsPollingInterval(...);
    $callback = $item->activeWhen(...);
    $callback = $item->hasActiveChildItems(...);
    $callback = SettingsCluster::prependClusterBreadcrumbs(...);
    $panel->requestPasswordResetRouteSlug('email')->resetPasswordRouteSlug('password')->databaseNotificationsPollingInterval(null);
    $item->activeWhen(null)->hasActiveChildItems();
    SettingsCluster::prependClusterBreadcrumbs([]);
}
