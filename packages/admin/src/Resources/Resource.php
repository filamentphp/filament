<?php

namespace Filament\Resources;

use Closure;
use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\Navigation\NavigationItem;
use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Resource
{
    use Macroable {
        __call as dynamicMacroCall;
    }

    protected static ?string $breadcrumb = null;

    protected static bool $isGloballySearchable = true;

    /**
     * @deprecated Use `$modelLabel` instead.
     */
    protected static ?string $label = null;

    protected static ?string $modelLabel = null;

    protected static ?string $model = null;

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $activeNavigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static ?string $recordRouteKeyName = null;

    protected static bool $shouldRegisterNavigation = true;

    /**
     * @deprecated Use `$pluralModelLabel` instead.
     */
    protected static ?string $pluralLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = null;

    protected static ?string $slug = null;

    protected static string | array $middlewares = [];

    protected static string | array $withoutRouteMiddleware = [];

    protected static int $globalSearchResultsLimit = 50;

    protected static bool $shouldAuthorizeWithGate = false;

    protected static bool $shouldIgnorePolicies = false;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        if (! static::canViewAny()) {
            return;
        }

        Filament::registerNavigationItems(static::getNavigationItems());
    }

    public static function getNavigationItems(): array
    {
        $routeBaseName = static::getRouteBaseName();

        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs("{$routeBaseName}.*"))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function resolveRecordRouteBinding($key): ?Model
    {
        return app(static::getModel())
            ->resolveRouteBindingQuery(static::getEloquentQuery(), $key, static::getRecordRouteKeyName())
            ->first();
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        $user = Filament::auth()->user();
        $model = static::getModel();

        if (static::shouldAuthorizeWithGate()) {
            return Gate::forUser($user)->check($action, $record ?? $model);
        }

        if (static::shouldIgnorePolicies()) {
            return true;
        }

        $policy = Gate::getPolicyFor($model);
        if ($policy === null) {
            return true;
        }

        if (! method_exists($policy, $action)) {
            return true;
        }

        return Gate::forUser($user)->check($action, $record ?? $model);
    }

    public static function authorizeWithGate(bool $condition = true): void
    {
        static::$shouldAuthorizeWithGate = $condition;
    }

    public static function ignorePolicies(bool $condition = true): void
    {
        static::$shouldIgnorePolicies = $condition;
    }

    public static function shouldAuthorizeWithGate(): bool
    {
        return static::$shouldAuthorizeWithGate;
    }

    public static function shouldIgnorePolicies(): bool
    {
        return static::$shouldIgnorePolicies;
    }

    public static function canViewAny(): bool
    {
        return static::can('viewAny');
    }

    public static function canCreate(): bool
    {
        return static::can('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::can('update', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::can('delete', $record);
    }

    public static function canDeleteAny(): bool
    {
        return static::can('deleteAny');
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::can('forceDelete', $record);
    }

    public static function canForceDeleteAny(): bool
    {
        return static::can('forceDeleteAny');
    }

    public static function canReorder(): bool
    {
        return static::can('reorder');
    }

    public static function canReplicate(Model $record): bool
    {
        return static::can('replicate', $record);
    }

    public static function canRestore(Model $record): bool
    {
        return static::can('restore', $record);
    }

    public static function canRestoreAny(): bool
    {
        return static::can('restoreAny');
    }

    public static function canGloballySearch(): bool
    {
        return static::$isGloballySearchable && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        return static::can('view', $record);
    }

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? Str::headline(static::getPluralModelLabel());
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function getGloballySearchableAttributes(): array
    {
        $titleAttribute = static::getRecordTitleAttribute();

        if ($titleAttribute === null) {
            return [];
        }

        return [$titleAttribute];
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return static::getRecordTitle($record);
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        if (static::hasPage('edit') && static::canEdit($record)) {
            return static::getUrl('edit', ['record' => $record]);
        }

        if (static::hasPage('view') && static::canView($record)) {
            return static::getUrl('view', ['record' => $record]);
        }

        return null;
    }

    public static function getGlobalSearchResultsLimit(): int
    {
        return static::$globalSearchResultsLimit;
    }

    public static function getGlobalSearchResults(string $searchQuery): Collection
    {
        $searchQuery = strtolower($searchQuery);

        $query = static::getGlobalSearchEloquentQuery();

        foreach (explode(' ', $searchQuery) as $searchQueryWord) {
            $query->where(function (Builder $query) use ($searchQueryWord) {
                $isFirst = true;

                foreach (static::getGloballySearchableAttributes() as $attributes) {
                    static::applyGlobalSearchAttributeConstraint($query, Arr::wrap($attributes), $searchQueryWord, $isFirst);
                }
            });
        }

        return $query
            ->limit(static::getGlobalSearchResultsLimit())
            ->get()
            ->map(function (Model $record): ?GlobalSearchResult {
                $url = static::getGlobalSearchResultUrl($record);

                if (blank($url)) {
                    return null;
                }

                return new GlobalSearchResult(
                    title: static::getGlobalSearchResultTitle($record),
                    url: $url,
                    details: static::getGlobalSearchResultDetails($record),
                    actions: static::getGlobalSearchResultActions($record),
                );
            })
            ->filter();
    }

    /**
     * @deprecated Use `getModelLabel()` instead.
     */
    public static function getLabel(): ?string
    {
        return static::$label;
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? static::getLabel() ?? get_model_label(static::getModel());
    }

    public static function getModel(): string
    {
        return static::$model ?? (string) Str::of(class_basename(static::class))
            ->beforeLast('Resource')
            ->prepend('App\\Models\\');
    }

    public static function getPages(): array
    {
        return [];
    }

    /**
     * @deprecated Use `getPluralModelLabel()` instead.
     */
    public static function getPluralLabel(): ?string
    {
        return static::$pluralLabel;
    }

    public static function getPluralModelLabel(): string
    {
        if (filled($label = static::$pluralModelLabel ?? static::getPluralLabel())) {
            return $label;
        }

        if (locale_has_pluralization()) {
            return Str::plural(static::getModelLabel());
        }

        return static::getModelLabel();
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute;
    }

    public static function getRecordTitle(?Model $record): string | Htmlable | null
    {
        return $record?->getAttribute(static::getRecordTitleAttribute()) ?? static::getModelLabel();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getRouteBaseName(): string
    {
        $slug = static::getSlug();

        return "filament.resources.{$slug}";
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return static::$recordRouteKeyName;
    }

    public static function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            Route::name("{$slug}.")
                ->prefix($slug)
                ->middleware(static::getMiddlewares())
                ->withoutMiddleware(static::getWithoutRouteMiddleware())
                ->group(function () {
                    foreach (static::getPages() as $name => $page) {
                        Route::get($page['route'], $page['class'])->name($name);
                    }
                });
        };
    }

    public static function getMiddlewares(): string | array
    {
        return static::$middlewares;
    }

    public static function getWithoutRouteMiddleware(): string | array
    {
        return static::$withoutRouteMiddleware;
    }

    public static function getSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return Str::of(static::getModel())
            ->afterLast('\\Models\\')
            ->plural()
            ->explode('\\')
            ->map(fn (string $string) => Str::of($string)->kebab()->slug())
            ->implode('/');
    }

    public static function getUrl($name = 'index', $params = [], $isAbsolute = true): string
    {
        $routeBaseName = static::getRouteBaseName();

        return route("{$routeBaseName}.{$name}", $params, $isAbsolute);
    }

    public static function hasPage($page): bool
    {
        return array_key_exists($page, static::getPages());
    }

    public static function hasRecordTitle(): bool
    {
        return static::getRecordTitleAttribute() !== null;
    }

    protected static function applyGlobalSearchAttributeConstraint(Builder $query, array $searchAttributes, string $searchQuery, bool &$isFirst): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $model = $query->getModel();

        foreach ($searchAttributes as $searchAttribute) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($searchAttribute),
                function (Builder $query) use ($databaseConnection, $searchAttribute, $searchOperator, $searchQuery, $whereClause): Builder {
                    $searchColumn = match ($databaseConnection->getDriverName()) {
                        'pgsql' => "{$searchAttribute}::text",
                        default => "json_extract({$searchAttribute}, '$')",
                    };

                    return $query->{"{$whereClause}Raw"}(
                        "lower({$searchColumn}) {$searchOperator} ?",
                        "%{$searchQuery}%",
                    );
                },
                fn (Builder $query): Builder => $query->when(
                    Str::of($searchAttribute)->contains('.'),
                    fn ($query) => $query->{"{$whereClause}Relation"}(
                        (string) Str::of($searchAttribute)->beforeLast('.'),
                        (string) Str::of($searchAttribute)->afterLast('.'),
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                    fn ($query) => $query->{$whereClause}(
                        $searchAttribute,
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                ),
            );

            $isFirst = false;
        }

        return $query;
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    protected static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function navigationGroup(?string $group): void
    {
        static::$navigationGroup = $group;
    }

    protected static function getNavigationIcon(): string
    {
        return static::$navigationIcon ?? 'heroicon-o-collection';
    }

    public static function navigationIcon(?string $icon): void
    {
        static::$navigationIcon = $icon;
    }

    protected static function getActiveNavigationIcon(): string
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    protected static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? Str::headline(static::getPluralModelLabel());
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

    public static function navigationSort(?int $sort): void
    {
        static::$navigationSort = $sort;
    }

    protected static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }
}
