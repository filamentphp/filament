<?php

namespace Filament\Resources\Resource\Concerns;

use Filament\Actions\Action;
use Filament\GlobalSearch\GlobalSearchResult;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;

trait HasGlobalSearch
{
    protected static int $globalSearchResultsLimit = 50;

    protected static ?bool $isGlobalSearchForcedCaseInsensitive = null;

    protected static ?bool $shouldSplitGlobalSearchTerms = null;

    protected static bool $isGloballySearchable = true;

    protected static ?int $globalSearchSort = null;

    public static function canGloballySearch(): bool
    {
        return static::$isGloballySearchable && count(static::getGloballySearchableAttributes()) && static::canAccess();
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

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return static::getRecordTitle($record);
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        // In the future, Filament will support global search in nested resources.
        // For now, you must specify custom global search result URLs to do so,
        // since there are missing URL parameters from the parent records.
        if (static::getParentResourceRegistration()) {
            return null;
        }

        $canView = static::canView($record);

        if (static::hasPage('view') && $canView) {
            return static::getUrl('view', ['record' => $record]);
        }

        $canEdit = static::canEdit($record);

        if (static::hasPage('edit') && $canEdit) {
            return static::getUrl('edit', ['record' => $record]);
        }

        if ($canView) {
            return static::getUrl(parameters: [
                'tableAction' => 'view',
                'tableActionRecord' => $record,
            ]);
        }

        if ($canEdit) {
            return static::getUrl(parameters: [
                'tableAction' => 'edit',
                'tableActionRecord' => $record,
            ]);
        }

        return null;
    }

    public static function getGlobalSearchResultsLimit(): int
    {
        return static::$globalSearchResultsLimit;
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void {}

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
                    actions: array_map(
                        fn (Action $action) => $action->hasRecord() ? $action : $action->record($record),
                        static::getGlobalSearchResultActions($record),
                    ),
                );
            })
            ->filter();
    }

    public static function isGlobalSearchForcedCaseInsensitive(): ?bool
    {
        return static::$isGlobalSearchForcedCaseInsensitive;
    }

    public static function shouldSplitGlobalSearchTerms(): bool
    {
        return static::$shouldSplitGlobalSearchTerms ?? true;
    }

    protected static function applyGlobalSearchAttributeConstraints(Builder $query, string $search): void
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $search = generate_search_term_expression($search, static::isGlobalSearchForcedCaseInsensitive(), $databaseConnection);

        if (! static::shouldSplitGlobalSearchTerms()) {
            $isFirst = true;

            foreach (static::getGloballySearchableAttributes() as $attributes) {
                static::applyGlobalSearchAttributeConstraint(
                    query: $query,
                    search: $search,
                    searchAttributes: Arr::wrap($attributes),
                    isFirst: $isFirst,
                );
            }

            return;
        }

        $searchWords = array_filter(
            str_getcsv(preg_replace('/(\s|\x{3164}|\x{1160})+/u', ' ', $search), separator: ' ', escape: '\\'),
            fn ($word): bool => filled($word),
        );

        foreach ($searchWords as $searchWord) {
            $query->where(function (Builder $query) use ($searchWord): void {
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
        $isForcedCaseInsensitive = static::isGlobalSearchForcedCaseInsensitive();

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        foreach ($searchAttributes as $searchAttribute) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                str($searchAttribute)->contains('.'),
                function (Builder $query) use ($databaseConnection, $isForcedCaseInsensitive, $searchAttribute, $search, $whereClause): Builder {
                    return $query->{"{$whereClause}Has"}(
                        (string) str($searchAttribute)->beforeLast('.'),
                        fn (Builder $query) => $query->where(
                            generate_search_column_expression($query->qualifyColumn((string) str($searchAttribute)->afterLast('.')), $isForcedCaseInsensitive, $databaseConnection),
                            'like',
                            "%{$search}%",
                        ),
                    );
                },
                fn (Builder $query) => $query->{$whereClause}(
                    generate_search_column_expression($query->qualifyColumn($searchAttribute), $isForcedCaseInsensitive, $databaseConnection),
                    'like',
                    "%{$search}%",
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

    public static function getGlobalSearchSort(): ?int
    {
        return static::$globalSearchSort;
    }

    public static function globalSearchSort(?int $sort): void
    {
        static::$globalSearchSort = $sort;
    }
}
