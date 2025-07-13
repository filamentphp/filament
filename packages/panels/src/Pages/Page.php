<?php

namespace Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use UnitEnum;

use function Filament\Support\original_request;

abstract class Page extends BasePage
{
    use Concerns\CanAuthorizeAccess;
    use Concerns\HasErrorNotifications;
    use Concerns\HasRoutes;
    use Concerns\HasSubNavigation;
    use Concerns\InteractsWithHeaderActions;

    protected static string $layout;

    /**
     * @var class-string<Cluster> | null
     */
    protected static ?string $cluster = null;

    protected static bool $isDiscovered = true;

    protected static string | UnitEnum | null $navigationGroup = null;

    protected static ?string $navigationBadgeTooltip = null;

    protected static ?string $navigationParentItem = null;

    protected static string | BackedEnum | null $navigationIcon = null;

    protected static string | BackedEnum | null $activeNavigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament-panels::pages.page';

    public function getLayout(): string
    {
        return static::$layout ?? 'filament-panels::components.layout.index';
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if (blank($panel) || ($panel = Filament::getPanel($panel))->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        return route(static::getRouteName($panel), $parameters, $isAbsolute);
    }

    public static function registerRoutes(Panel $panel): void
    {
        if (filled(static::getCluster())) {
            Route::name(static::prependClusterRouteBaseName($panel, 'pages.'))
                ->prefix(static::prependClusterSlug($panel, ''))
                ->group(fn () => static::routes($panel));

            return;
        }

        Route::name('pages.')->group(fn () => static::routes($panel));
    }

    public static function registerNavigationItems(): void
    {
        if (filled(static::getCluster())) {
            return;
        }

        if (! static::shouldRegisterNavigation()) {
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
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn (): bool => original_request()->routeIs(static::getNavigationItemActiveRoutePattern()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getNavigationItemActiveRoutePattern(): string
    {
        return static::getRouteName();
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        $routeName = 'pages.' . static::getRelativeRouteName($panel);
        $routeName = static::prependClusterRouteBaseName($panel, $routeName);

        return $panel->generateRouteName($routeName);
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs([]);
        }

        return [];
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::$navigationGroup;
    }

    public static function getNavigationParentItem(): ?string
    {
        return static::$navigationParentItem;
    }

    public static function getActiveNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon;
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    /**
     * @return string | array<string> | null
     */
    public static function getNavigationBadgeColor(): string | array | null
    {
        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return static::$navigationBadgeTooltip;
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    public function getFooter(): ?View
    {
        return null;
    }

    public function getHeader(): ?View
    {
        return null;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    /**
     * @deprecated Use `getWidgetsSchemaComponents($this->getHeaderWidgets())` to transform widgets into schema components instead, which also filters their visibility.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleHeaderWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getHeaderWidgets());
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    /**
     * @deprecated Use `getWidgetsSchemaComponents($this->getFooterWidgets())` to transform widgets into schema components instead, which also filters their visibility.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleFooterWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getFooterWidgets());
    }

    /**
     * @deprecated Use `getWidgetsSchemaComponents()` to transform widgets into schema components instead, which also filters their visibility.
     *
     * @param  array<class-string<Widget> | WidgetConfiguration>  $widgets
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    protected function filterVisibleWidgets(array $widgets): array
    {
        return array_filter($widgets, fn (string | WidgetConfiguration $widget): bool => $this->normalizeWidgetClass($widget)::canView());
    }

    /**
     * @param  class-string<Widget> | WidgetConfiguration  $widget
     * @return class-string<Widget>
     */
    protected function normalizeWidgetClass(string | WidgetConfiguration $widget): string
    {
        if ($widget instanceof WidgetConfiguration) {
            return $widget->widget;
        }

        return $widget;
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getFooterWidgetsColumns(): int | array
    {
        return 2;
    }

    /**
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    /**
     * @return class-string<Cluster> | null
     */
    public static function getCluster(): ?string
    {
        return static::$cluster;
    }

    public static function prependClusterSlug(Panel $panel, string $slug): string
    {
        if (filled($cluster = static::getCluster())) {
            return $cluster::prependClusterSlug($panel, $slug);
        }

        return $slug;
    }

    public static function prependClusterRouteBaseName(Panel $panel, string $name): string
    {
        if (filled($cluster = static::getCluster())) {
            return $cluster::prependClusterRouteBaseName($panel, $name);
        }

        return $name;
    }

    public function content(Schema $schema): Schema
    {
        return $schema;
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [];
    }

    /**
     * @param  array<string | WidgetConfiguration>  $widgets
     * @param  array<string, mixed>  $data
     * @return array<Component | Action | ActionGroup>
     */
    public function getWidgetsSchemaComponents(array $widgets, array $data = []): array
    {
        return collect($widgets)
            ->values()
            ->filter(fn (string | WidgetConfiguration $widget): bool => $this->normalizeWidgetClass($widget)::canView())
            ->map(fn (string | WidgetConfiguration $widget, int $widgetKey): Livewire => Livewire::make(
                $widgetClass = $this->normalizeWidgetClass($widget),
                fn (): array => [
                    ...$this->getWidgetData(),
                    ...$data,
                    ...(($widget instanceof WidgetConfiguration) ? [
                        ...$widget->widget::getDefaultProperties(),
                        ...$widget->getProperties(),
                    ] : $widget::getDefaultProperties()),
                    ...(property_exists($this, 'filters') ? ['pageFilters' => $this->filters] : []),
                ],
            )->key("{$widgetClass}-{$widgetKey}")->liberatedFromContainerGrid())
            ->all();
    }

    public function headerWidgets(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::PAGE_HEADER_WIDGETS_START),
                Grid::make($this->getHeaderWidgetsColumns())
                    ->schema($widgets = $this->getWidgetsSchemaComponents($this->getHeaderWidgets())),
                RenderHook::make(PanelsRenderHook::PAGE_HEADER_WIDGETS_END),
            ])
            ->hidden(empty($widgets));
    }

    public function footerWidgets(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::PAGE_FOOTER_WIDGETS_START),
                Grid::make($this->getFooterWidgetsColumns())
                    ->schema($widgets = $this->getWidgetsSchemaComponents($this->getFooterWidgets())),
                RenderHook::make(PanelsRenderHook::PAGE_FOOTER_WIDGETS_END),
            ])
            ->hidden(empty($widgets));
    }

    public function getDefaultTestingSchemaName(): ?string
    {
        return 'content';
    }
}
