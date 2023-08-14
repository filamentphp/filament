<?php

namespace Filament\Pages;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

abstract class Page extends BasePage
{
    use Concerns\HasRoutes;
    use Concerns\InteractsWithHeaderActions;

    protected static bool $isDiscovered = true;

    protected static string $layout = 'filament::components.layouts.app';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $activeNavigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static bool $shouldRegisterNavigation = true;

    public static string $formActionsAlignment = 'start';

    public static bool $formActionsAreSticky = false;

    public static bool $hasInlineLabels = false;

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, string $panel = null, Model $tenant = null): string
    {
        $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());

        return route(static::getRouteName($panel), $parameters, $isAbsolute);
    }

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        Filament::getCurrentPanel()
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
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getRouteName(string $panel = null): string
    {
        $panel ??= Filament::getCurrentPanel()->getId();

        return (string) str(static::getSlug())
            ->replace('/', '.')
            ->prepend("filament.{$panel}.pages.");
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon;
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public static function getNavigationBadgeColor(): string | array | null
    {
        return null;
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
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    public function getVisibleHeaderWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getHeaderWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getHeaderWidgetsColumns(): int | string | array
    {
        return 2;
    }

    /**
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    public function getVisibleFooterWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getFooterWidgets());
    }

    /**
     * @param  array<class-string>  $widgets
     * @return array<class-string>
     */
    protected function filterVisibleWidgets(array $widgets): array
    {
        return array_filter($widgets, fn (string $widget): bool => $widget::canView());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getFooterWidgetsColumns(): int | string | array
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

    public static function stickyFormActions(bool $condition = true): void
    {
        static::$formActionsAreSticky = $condition;
    }

    public static function alignFormActionsStart(): void
    {
        static::$formActionsAlignment = 'start';
    }

    public static function alignFormActionsCenter(): void
    {
        static::$formActionsAlignment = 'center';
    }

    public static function alignFormactionsEnd(): void
    {
        static::$formActionsAlignment = 'end';
    }

    /**
     * @deprecated Use `alignFormActionsStart()` instead
     */
    public static function alignFormActionsLeft(): void
    {
        static::alignFormActionsStart();
    }

    /**
     * @deprecated Use `alignFormActionsEnd()` instead
     */
    public static function alignFormActionsRight(): void
    {
        static::alignFormActionsEnd();
    }

    public function getFormActionsAlignment(): string
    {
        return static::$formActionsAlignment;
    }

    public function areFormActionsSticky(): bool
    {
        return static::$formActionsAreSticky;
    }

    public function hasInlineLabels(): bool
    {
        return static::$hasInlineLabels;
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    public static function formActionsAlignment(string $alignment): void
    {
        static::$formActionsAlignment = $alignment;
    }

    public static function inlineLabels(bool $condition = true): void
    {
        static::$hasInlineLabels = $condition;
    }
}
