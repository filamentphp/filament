<?php

namespace Filament\Resources\Resource\Concerns;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

use function Filament\Support\original_request;

trait HasNavigation
{
    protected static ?SubNavigationPosition $subNavigationPosition = null;

    protected static bool $shouldRegisterNavigation = true;

    protected static string | Htmlable | null $navigationBadgeTooltip = null;

    protected static string | UnitEnum | null $navigationGroup = null;

    protected static ?string $navigationParentItem = null;

    protected static string | BackedEnum | null $navigationIcon = null;

    protected static string | BackedEnum | null $activeNavigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    public static function registerNavigationItems(): void
    {
        if (filled(static::getCluster())) {
            return;
        }

        if (! static::shouldRegisterNavigation()) {
            return;
        }

        if (static::getParentResourceRegistration()) {
            return;
        }

        if (! static::canAccess()) {
            return;
        }

        Filament::getCurrentOrDefaultPanel()
            ->navigationItems(static::getNavigationItems());
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        if (! static::hasPage('index')) {
            return [];
        }

        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => original_request()->routeIs(static::getNavigationItemActiveRoutePattern()))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    /**
     * @return string | array<string>
     */
    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return static::getRouteBaseName() . '.*';
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        if (filled(static::$subNavigationPosition)) {
            return static::$subNavigationPosition;
        }

        if (filled($cluster = static::getCluster())) {
            return $cluster::getSubNavigationPosition();
        }

        return Filament::getSubNavigationPosition();
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::$navigationGroup;
    }

    public static function getNavigationParentItem(): ?string
    {
        return static::$navigationParentItem;
    }

    public static function navigationGroup(?string $group): void
    {
        static::$navigationGroup = $group;
    }

    public static function navigationParentItem(?string $item): void
    {
        static::$navigationParentItem = $item;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon;
    }

    public static function navigationIcon(string | BackedEnum $icon): void
    {
        static::$navigationIcon = $icon;
    }

    public static function getActiveNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::getTitleCasePluralModelLabel();
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeTooltip(): string | Htmlable | null
    {
        return static::$navigationBadgeTooltip;
    }

    /**
     * @return string | array<string> | null
     */
    public static function getNavigationBadgeColor(): string | array | null
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    public static function navigationLabel(?string $label): void
    {
        static::$navigationLabel = $label;
    }

    public static function navigationSort(?int $sort): void
    {
        static::$navigationSort = $sort;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    /**
     * @return array<NavigationItem | NavigationGroup>
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [];
    }
}
