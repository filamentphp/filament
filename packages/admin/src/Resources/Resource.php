<?php

namespace Filament\Resources;

use Closure;
use Filament\Facades\Filament;
use Filament\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Resource
{
    protected static ?string $breadcrumb = null;

    protected static ?string $label = null;

    protected static ?string $model = null;

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static ?string $pluralLabel = null;

    protected static ?string $slug = null;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function registerNavigationItems(): void
    {
        $routeBaseName = static::getRouteBaseName();

        Filament::registerNavigationItems([
            NavigationItem::make()
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs("{$routeBaseName}*"))
                ->label(static::getNavigationLabel())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ]);
    }

    public static function routes(): array
    {
        return [];
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? Str::title(static::getPluralLabel());
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function getIndexRouteName(): string
    {
        $base = static::getRouteBaseName();

        return "{$base}.index";
    }

    public static function getLabel(): string
    {
        return static::$label ?? (string) Str::of(class_basename(static::getModel()))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function getModel(): string
    {
        return static::$model ?? (string) Str::of(class_basename(static::class))
            ->beforeLast('Resource')
            ->prepend('App\\Models\\');
    }

    public static function getPluralLabel(): string
    {
        return static::$pluralLabel ?? Str::plural(static::getLabel());
    }

    public static function getRecordRouteName(): string
    {
        $base = static::getRouteBaseName();

        return "{$base}.edit";
    }

    public static function getRecordUrl(Model $record): string
    {
        return route(static::getRecordRouteName(), ['record' => $record]);
    }

    public static function getRouteBaseName(): string
    {
        $slug = static::getSlug();

        return "filament.resources.{$slug}";
    }

    public static function getRouteName(): string
    {
        return static::getIndexRouteName();
    }

    public static function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            Route::name("{$slug}.")->prefix($slug)->group(function () use ($slug) {
                foreach (static::routes() as $route) {
                    Route::get($route['path'], $route['page'])->name($route['name']);
                }
            });
        };
    }

    public static function getSlug(): string
    {
        return static::$slug ?? (string) Str::of(class_basename(static::getModel()))
            ->plural()
            ->kebab();
    }

    public static function getUrl(): string
    {
        return route(static::getRouteName());
    }

    protected static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    protected static function getNavigationIcon(): string
    {
        return static::$navigationIcon ?? 'heroicon-o-collection';
    }

    protected static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? Str::title(static::getPluralLabel());
    }

    protected static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    protected static function getNavigationUrl(): string
    {
        return static::getUrl();
    }
}
