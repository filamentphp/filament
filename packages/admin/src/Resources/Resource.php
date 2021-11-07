<?php

namespace Filament\Resources;

use Filament\Facades\Filament;
use Filament\NavigationItem;
use Illuminate\Support\Str;

class Resource
{
    public static ?string $label = null;

    public static ?string $model = null;

    public static ?string $navigationGroup = null;

    public static ?string $navigationIcon = null;

    public static ?string $navigationLabel = null;

    public static ?int $navigationSort = null;

    public static ?string $pluralLabel = null;

    public static ?string $routeName = null;

    public static ?string $slug = null;

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

    protected static function getLabel(): string
    {
        return static::$label ?? (string) Str::of(class_basename(static::getModel()))
            ->kebab()
            ->replace('-', ' ');
    }

    protected static function getModel(): string
    {
        return static::$model ?? (string) Str::of(class_basename(static::class))
            ->beforeLast('Resource')
            ->prepend('App\\Models\\');
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
        return '';
    }

    protected static function getPluralLabel(): string
    {
        return static::$pluralLabel ?? Str::plural(static::getLabel());
    }

    protected static function getRouteName(): string
    {
        $slug = static::getSlug();

        return static::$routeName ?? "filament.resources.{$slug}";
    }

    protected static function getSlug(): string
    {
        return static::$slug ?? (string) Str::of(class_basename(static::getModel()))
            ->plural()
            ->kebab();
    }

    protected static function getUrl(): string
    {
        return route(static::getRouteName());
    }
}
