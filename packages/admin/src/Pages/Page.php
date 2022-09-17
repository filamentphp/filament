<?php

namespace Filament\Pages;

use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Http\Livewire\Concerns\CanNotify;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Contracts\RendersFormComponentActionModal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Page extends Component implements Forms\Contracts\HasForms, RendersFormComponentActionModal
{
    use CanNotify;
    use Concerns\HasActions;

    protected static string $layout = 'filament::components.layouts.app';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = null;

    protected static ?string $title = null;

    protected static string $view;

    protected static string | array $middlewares = [];

    public static ?Closure $reportValidationErrorUsing = null;

    protected ?string $maxContentWidth = null;

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        Filament::registerNavigationItems(static::getNavigationItems());
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

    public static function getRouteName(): string
    {
        $slug = static::getSlug();

        return "filament.pages.{$slug}";
    }

    public static function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            Route::get($slug, static::class)
                ->middleware(static::getMiddlewares())
                ->name($slug);
        };
    }

    public static function getMiddlewares(): string | array
    {
        return static::$middlewares;
    }

    public static function getSlug(): string
    {
        return static::$slug ?? Str::of(static::$title ?? class_basename(static::class))
            ->kebab()
            ->slug();
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true): string
    {
        return route(static::getRouteName(), $parameters, $isAbsolute);
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, $this->getLayoutData());
    }

    protected function getBreadcrumbs(): array
    {
        return [];
    }

    protected static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    protected static function getNavigationIcon(): string
    {
        return static::$navigationIcon ?? 'heroicon-o-document-text';
    }

    protected static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    protected static function getNavigationBadge(): ?string
    {
        return null;
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return null;
    }

    protected static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    protected static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    protected function getActions(): array
    {
        return [];
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

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function getHeading(): string
    {
        return $this->getTitle();
    }

    protected function getTitle(): string
    {
        return static::$title ?? (string) Str::of(class_basename(static::class))
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

    protected static function shouldRegisterNavigation(): bool
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
}
