<?php

namespace Filament\Pages;

use Filament\Facades\Filament;
use Filament\NavigationItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Page extends Component
{
    public static string $layout = 'filament::components.layouts.app';

    public static ?string $navigationGroup = null;

    public static ?string $navigationIcon = null;

    public static ?string $navigationLabel = null;

    public static ?int $navigationSort = null;

    public static ?string $routeName = null;

    public static ?string $slug = null;

    public static ?string $title = null;

    public static string $view;

    public static function registerNavigationItems(): void
    {
        Filament::registerNavigationItems([
            NavigationItem::make()
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs([
                    $routeName = static::getRouteName(),
                    "{$routeName}*",
                ]))
                ->label(static::getNavigationLabel())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ]);
    }

    public static function registerRoutes(): void
    {
        //
    }

    public static function getSlug(): string
    {
        return static::$slug ?? (string) Str::kebab(static::getTitle());
    }

    public static function getTitle(): string
    {
        return static::$title ?? (string) Str::of(class_basename(static::class))
                ->kebab()
                ->replace('-', ' ')
                ->title();
    }

    public static function getUrl(): string
    {
        return route(static::getRouteName());
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, $this->getLayoutData());
    }

    protected static function getBreadcrumbs(): array
    {
        return [
            static::getTitle(),
        ];
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
        return static::$navigationLabel ?? static::getTitle();
    }

    protected static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    protected static function getNavigationUrl(): string
    {
        return '';
    }

    protected static function getRouteName(): string
    {
        $slug = static::getSlug();

        return static::$routeName ?? "filament.pages.{$slug}";
    }

    protected function getLayoutData(): array
    {
        return [
            'breadcrumbs' => static::getBreadcrumbs(),
            'title' => static::getTitle(),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'title' => static::getTitle(),
        ];
    }
}
