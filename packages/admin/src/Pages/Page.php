<?php

namespace Filament\Pages;

use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Form;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class Page extends Component
{
    protected static string $layout = 'filament::components.layouts.app';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static ?string $slug = null;

    protected static ?string $title = null;

    protected static string $view;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function registerNavigationItems(): void
    {
        Filament::registerNavigationItems([
            NavigationItem::make()
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs([
                    $routeName = static::getRouteName(),
                    "{$routeName}*",
                ]))
                ->label(static::getNavigationLabel())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ]);
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

            Route::get($slug, static::class)->name($slug);
        };
    }

    public static function getSlug(): string
    {
        return static::$slug ?? Str::kebab(static::$title ?? class_basename(static::class));
    }

    public static function getUrl(): string
    {
        return route(static::getRouteName());
    }

    protected function notify(string $status, string $message): void
    {
        session()->flash('notification', [
            'message' => $message,
            'status' => $status,
        ]);
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

    protected static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    protected static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    protected function getActions(): array | View | null
    {
        return null;
    }

    protected function getFooter(): ?View
    {
        return null;
    }

    protected function getHeader(): ?View
    {
        return null;
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

    protected function getLayoutData(): array
    {
        return [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => $this->getTitle(),
        ];
    }

    protected function getViewData(): array
    {
        return [];
    }
}
