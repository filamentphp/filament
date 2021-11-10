<?php

namespace Filament\Resources;

use Closure;
use Filament\Facades\Filament;
use Filament\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
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

    protected static ?string $primaryAttribute = null;

    protected static ?string $slug = null;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function registerNavigationItems(): void
    {
        if (! static::canAccess()) {
            return;
        }

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

    public static function pages(): array
    {
        return [];
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        $policy = Gate::getPolicyFor(static::getModel());

        if ($policy === null || ! method_exists($policy, $action)) {
            return true;
        }

        return Gate::check($action, $record);
    }

    public static function canAccess(): bool
    {
        return static::can('viewAny');
    }

    public static function canCreate(): bool
    {
        return static::hasPage('create') && static::can('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::hasPage('edit') && static::can('update');
    }

    public static function canDelete(Model $record): bool
    {
        return static::hasPage('delete') && static::can('delete');
    }

    public static function canView(Model $record): bool
    {
        return static::hasPage('view') && static::can('view');
    }

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? Str::title(static::getPluralLabel());
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
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

    public static function getPrimaryAttribute(): ?string
    {
        return static::$primaryAttribute;
    }

    public static function getPrimaryAttributeForModel(Model $model): ?string
    {
        return $model->getAttribute(static::getPrimaryAttribute());
    }

    public static function getRouteBaseName(): string
    {
        $slug = static::getSlug();

        return "filament.resources.{$slug}";
    }

    public static function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            Route::name("{$slug}.")->prefix($slug)->group(function () use ($slug) {
                foreach (static::pages() as $name => $page) {
                    Route::get($page['route'], $page['class'])->name($name);
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

    public static function getUrl($name = 'index', $params = []): string
    {
        $routeBaseName = static::getRouteBaseName();

        return route("{$routeBaseName}.{$name}", $params);
    }

    public static function hasPage($page): bool
    {
        return array_key_exists($page, static::pages());
    }

    public static function hasPrimaryAttribute(): bool
    {
        return static::getPrimaryAttribute() !== null;
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
