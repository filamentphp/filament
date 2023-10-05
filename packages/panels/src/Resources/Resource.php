<?php

namespace Filament\Resources;

use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Macroable;

use function Filament\authorize;
use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;

abstract class Resource
{
    use Macroable {
        Macroable::__call as dynamicMacroCall;
    }

    protected static ?string $breadcrumb = null;

    protected static bool $isDiscovered = true;

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

    protected static ?string $tenantOwnershipRelationshipName = null;

    protected static ?string $tenantRelationshipName = null;

    /**
     * @var string | array<string>
     */
    protected static string | array $routeMiddleware = [];

    /**
     * @var string | array<string>
     */
    protected static string | array $withoutRouteMiddleware = [];

    protected static int $globalSearchResultsLimit = 50;

    protected static bool $shouldCheckPolicyExistence = true;

    protected static bool $shouldSkipAuthorization = false;

    protected static ?bool $isGlobalSearchForcedCaseInsensitive = null;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist;
    }

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        if (! static::canViewAny()) {
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
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.*'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function resolveRecordRouteBinding(int | string $key): ?Model
    {
        return app(static::getModel())
            ->resolveRouteBindingQuery(static::getEloquentQuery(), $key, static::getRecordRouteKeyName())
            ->first();
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        $model = static::getModel();

        try {
            return authorize($action, $record ?? $model, static::shouldCheckPolicyExistence())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    /**
     * @throws AuthorizationException
     */
    public static function authorize(string $action, ?Model $record = null): ?Response
    {
        if (static::shouldSkipAuthorization()) {
            return null;
        }

        $model = static::getModel();

        try {
            return authorize($action, $record ?? $model, static::shouldCheckPolicyExistence());
        } catch (AuthorizationException $exception) {
            return $exception->toResponse();
        }
    }

    public static function checkPolicyExistence(bool $condition = true): void
    {
        static::$shouldCheckPolicyExistence = $condition;
    }

    public static function skipAuthorization(bool $condition = true): void
    {
        static::$shouldSkipAuthorization = $condition;
    }

    public static function shouldCheckPolicyExistence(): bool
    {
        return static::$shouldCheckPolicyExistence;
    }

    public static function shouldSkipAuthorization(): bool
    {
        return static::$shouldSkipAuthorization;
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

    public static function canView(Model $record): bool
    {
        return static::can('view', $record);
    }

    public static function authorizeViewAny(): void
    {
        static::authorize('viewAny');
    }

    public static function authorizeCreate(): void
    {
        static::authorize('create');
    }

    public static function authorizeEdit(Model $record): void
    {
        static::authorize('update', $record);
    }

    public static function authorizeView(Model $record): void
    {
        static::authorize('view', $record);
    }

    public static function canGloballySearch(): bool
    {
        return static::$isGloballySearchable && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? Str::headline(static::getPluralModelLabel());
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        if ($tenant = Filament::getTenant()) {
            static::scopeEloquentQueryToTenant($query, $tenant);
        }

        return $query;
    }

    public static function scopeEloquentQueryToTenant(Builder $query, ?Model $tenant): Builder
    {
        $tenant ??= Filament::getTenant();

        $tenantOwnershipRelationship = static::getTenantOwnershipRelationship($query->getModel());
        $tenantOwnershipRelationshipName = static::getTenantOwnershipRelationshipName();

        return match (true) {
            $tenantOwnershipRelationship instanceof MorphTo => $query->whereMorphedTo(
                $tenantOwnershipRelationshipName,
                $tenant,
            ),
            $tenantOwnershipRelationship instanceof BelongsTo => $query->whereBelongsTo(
                $tenant,
                $tenantOwnershipRelationshipName,
            ),
            default => $query->whereHas(
                $tenantOwnershipRelationshipName,
                fn (Builder $query) => $query->whereKey($tenant->getKey()),
            ),
        };
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        $titleAttribute = static::getRecordTitleAttribute();

        if ($titleAttribute === null) {
            return [];
        }

        return [$titleAttribute];
    }

    /**
     * @return array<Action>
     */
    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
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

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
    }

    public static function getGlobalSearchResults(string $search): Collection
    {
        $query = static::getGlobalSearchEloquentQuery();

        static::applyGlobalSearchAttributeConstraints($query, $search);

        static::modifyGlobalSearchQuery($query, $search);

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
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast('Resource')
            ->prepend('App\\Models\\');
    }

    /**
     * @return array<string, PageRegistration>
     */
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

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * @return array<class-string<Widget>>
     */
    public static function getWidgets(): array
    {
        return [];
    }

    public static function getRouteBaseName(?string $panel = null): string
    {
        $panel ??= Filament::getCurrentPanel()->getId();

        return (string) str(static::getSlug())
            ->replace('/', '.')
            ->prepend("filament.{$panel}.resources.");
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return static::$recordRouteKeyName;
    }

    public static function routes(Panel $panel): void
    {
        $slug = static::getSlug();

        Route::name(
            (string) str($slug)
                ->replace('/', '.')
                ->append('.'),
        )
            ->prefix($slug)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->group(function () use ($panel) {
                foreach (static::getPages() as $name => $page) {
                    $page->registerRoute($panel)?->name($name);
                }
            });
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return static::$routeMiddleware;
    }

    /**
     * @return string | array<string>
     */
    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return static::$withoutRouteMiddleware;
    }

    public static function getEmailVerifiedMiddleware(Panel $panel): string
    {
        return $panel->getEmailVerifiedMiddleware();
    }

    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return $panel->isEmailVerificationRequired();
    }

    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return $panel->getTenantBillingProvider()->getSubscribedMiddleware();
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return $panel->isTenantSubscriptionRequired();
    }

    public static function getSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return str(static::class)
            ->whenContains(
                '\\Resources\\',
                fn (Stringable $slug): Stringable => $slug->afterLast('\\Resources\\'),
                fn (Stringable $slug): Stringable => $slug->classBasename(),
            )
            ->beforeLast('Resource')
            ->plural()
            ->explode('\\')
            ->map(fn (string $string) => str($string)->kebab()->slug())
            ->implode('/');
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());

        $routeBaseName = static::getRouteBaseName(panel: $panel);

        return route("{$routeBaseName}.{$name}", $parameters, $isAbsolute);
    }

    public static function hasPage(string $page): bool
    {
        return array_key_exists($page, static::getPages());
    }

    public static function hasRecordTitle(): bool
    {
        return static::getRecordTitleAttribute() !== null;
    }

    public static function isGlobalSearchForcedCaseInsensitive(Builder $query): bool
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        return static::$isGlobalSearchForcedCaseInsensitive ?? match ($databaseConnection->getDriverName()) {
            'pgsql' => true,
            default => false,
        };
    }

    protected static function applyGlobalSearchAttributeConstraints(Builder $query, string $search): void
    {
        if (static::isGlobalSearchForcedCaseInsensitive($query)) {
            $search = Str::lower($search);
        }

        foreach (explode(' ', $search) as $searchWord) {
            $query->where(function (Builder $query) use ($searchWord) {
                $isFirst = true;

                foreach (static::getGloballySearchableAttributes() as $attributes) {
                    static::applyGlobalSearchAttributeConstraint(
                        query: $query,
                        search: $searchWord,
                        searchAttributes: Arr::wrap($attributes),
                        isFirst: $isFirst,
                    );
                }
            });
        }
    }

    /**
     * @param  array<string>  $searchAttributes
     */
    protected static function applyGlobalSearchAttributeConstraint(Builder $query, string $search, array $searchAttributes, bool &$isFirst): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $model = $query->getModel();

        $isForcedCaseInsensitive = static::isGlobalSearchForcedCaseInsensitive($query);

        foreach ($searchAttributes as $searchAttribute) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($searchAttribute),
                function (Builder $query) use ($databaseConnection, $isForcedCaseInsensitive, $searchAttribute, $search, $whereClause): Builder {
                    $searchColumn = match ($databaseConnection->getDriverName()) {
                        'pgsql' => "{$searchAttribute}::text",
                        default => $searchAttribute,
                    };

                    $caseAwareSearchColumn = $isForcedCaseInsensitive ?
                        new Expression("lower({$searchColumn})") :
                        $searchColumn;

                    return $query->$whereClause(
                        $caseAwareSearchColumn,
                        'like',
                        "%{$search}%",
                    );
                },
                fn (Builder $query): Builder => $query->when(
                    str($searchAttribute)->contains('.'),
                    function (Builder $query) use ($isForcedCaseInsensitive, $searchAttribute, $search, $whereClause): Builder {
                        $searchColumn = (string) str($searchAttribute)->afterLast('.');

                        $caseAwareSearchColumn = $isForcedCaseInsensitive ?
                            new Expression("lower({$searchColumn})") :
                            $searchColumn;

                        return $query->{"{$whereClause}Relation"}(
                            (string) str($searchAttribute)->beforeLast('.'),
                            $caseAwareSearchColumn,
                            'like',
                            "%{$search}%",
                        );
                    },
                    function ($query) use ($isForcedCaseInsensitive, $whereClause, $searchAttribute, $search) {
                        $caseAwareSearchColumn = $isForcedCaseInsensitive ?
                            new Expression("lower({$searchAttribute})") :
                            $searchAttribute;

                        return $query->{$whereClause}(
                            $caseAwareSearchColumn,
                            'like',
                            "%{$search}%",
                        );
                    },
                ),
            );

            $isFirst = false;
        }

        return $query;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function navigationGroup(?string $group): void
    {
        static::$navigationGroup = $group;
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon;
    }

    public static function navigationIcon(?string $icon): void
    {
        static::$navigationIcon = $icon;
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? Str::headline(static::getPluralModelLabel());
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

    public static function navigationSort(?int $sort): void
    {
        static::$navigationSort = $sort;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    public static function getTenantOwnershipRelationshipName(): string
    {
        return static::$tenantOwnershipRelationshipName ?? Filament::getTenantOwnershipRelationshipName();
    }

    public static function getTenantOwnershipRelationship(Model $record): Relation
    {
        $relationshipName = static::getTenantOwnershipRelationshipName();

        if (! $record->isRelation($relationshipName)) {
            $resourceClass = static::class;
            $recordClass = $record::class;

            throw new Exception("The model [{$recordClass}] does not have a relationship named [{$relationshipName}]. You can change the relationship being used by passing it to the [ownershipRelationship] argument of the [tenant()] method in configuration. You can change the relationship being used per-resource by setting it as the [\$tenantOwnershipRelationshipName] static property on the [{$resourceClass}] resource class.");
        }

        return $record->{$relationshipName}();
    }

    public static function getTenantRelationshipName(): string
    {
        return static::$tenantRelationshipName ?? (string) str(static::getModel())
            ->classBasename()
            ->pluralStudly()
            ->camel();
    }

    public static function getTenantRelationship(Model $tenant): Relation
    {
        $relationshipName = static::getTenantRelationshipName();

        if (! $tenant->isRelation($relationshipName)) {
            $resourceClass = static::class;
            $tenantClass = $tenant::class;

            throw new Exception("The model [{$tenantClass}] does not have a relationship named [{$relationshipName}]. You can change the relationship being used by setting it as the [\$tenantRelationshipName] static property on the [{$resourceClass}] resource class.");
        }

        return $tenant->{$relationshipName}();
    }

    /**
     * @return array<NavigationItem | NavigationGroup>
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [];
    }
}
