<?php

namespace Filament\Pages;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Context;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Contracts\RendersFormComponentActionModal;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

abstract class Page extends Component implements HasActions, RendersFormComponentActionModal
{
    use Concerns\InteractsWithHeaderActions;
    use InteractsWithActions;

    protected static bool $isDiscovered = true;

    protected static string $layout = 'filament::components.layouts.app';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = null;

    protected static ?string $title = null;

    protected ?string $heading = null;

    protected ?string $subheading = null;

    protected static string $view;

    protected static string | array $routeMiddleware = [];

    public static ?Closure $reportValidationErrorUsing = null;

    protected ?string $maxContentWidth = null;

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        Filament::getCurrentContext()
            ->navigationItems(static::getNavigationItems());
    }

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

    public static function getRouteName(?string $context = null): string
    {
        $context ??= Filament::getCurrentContext()->getId();

        $slug = static::getSlug();

        return "filament.{$context}.pages.{$slug}";
    }

    public static function routes(Context $context): void
    {
        $slug = static::getSlug();

        Route::get($slug, static::class)
            ->middleware(static::getRouteMiddleware($context))
            ->name($slug);
    }

    public static function getRouteMiddleware(Context $context): string | array
    {
        return array_merge(
            (static::isTenantSubscriptionRequired($context) ? [static::getTenantSubscribedMiddleware($context)] : []),
            static::$routeMiddleware,
        );
    }

    public static function getTenantSubscribedMiddleware(Context $context): string
    {
        return Filament::getTenantBillingProvider()->getSubscribedMiddleware();
    }

    public static function isTenantSubscriptionRequired(Context $context): bool
    {
        return $context->isTenantSubscriptionRequired();
    }

    public static function getSlug(): string
    {
        return static::$slug ?? str(static::$title ?? class_basename(static::class))
            ->kebab()
            ->slug();
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $context = null, ?Model $tenant = null): string
    {
        $parameters['tenant'] ??= ($tenant ?? Filament::getRoutableTenant());

        return route(static::getRouteName($context), $parameters, $isAbsolute);
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, $this->getLayoutData());
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getNavigationIcon(): string
    {
        return static::$navigationIcon ?? 'heroicon-o-document-text';
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

    public static function getNavigationBadgeColor(): ?string
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

    protected function getFooter(): ?View
    {
        return null;
    }

    protected function getHeader(): ?View
    {
        return null;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getVisibleHeaderWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getHeaderWidgets());
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function getVisibleFooterWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getFooterWidgets());
    }

    protected function filterVisibleWidgets(array $widgets): array
    {
        return array_filter($widgets, fn (string $widget): bool => $widget::canView());
    }

    protected function getFooterWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getHeading(): string
    {
        return $this->heading ?? $this->getTitle();
    }

    protected function getSubheading(): ?string
    {
        return $this->subheading;
    }

    protected function getTitle(): string
    {
        return static::$title ?? (string) str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    protected function getMaxContentWidth(): ?string
    {
        return $this->maxContentWidth;
    }

    protected function getLayoutData(): array
    {
        return [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => $this->getTitle(),
            'maxContentWidth' => $this->getMaxContentWidth(),
        ];
    }

    protected function getViewData(): array
    {
        return [];
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    protected function onValidationError(ValidationException $exception): void
    {
        if (! static::$reportValidationErrorUsing) {
            return;
        }

        (static::$reportValidationErrorUsing)($exception);
    }

    protected function halt(): void
    {
        throw new Halt();
    }
}
