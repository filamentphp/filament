<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

use function Filament\Support\apply_search_constraint;

trait CanSearchRecords
{
    protected ?bool $isSearchable = null;

    /**
     * @var array<string | Closure>
     */
    protected array $extraSearchableColumns = [];

    protected bool | Closure | null $persistsSearchInSession = false;

    protected bool | Closure | null $persistsColumnSearchesInSession = false;

    protected string | Closure | null $searchPlaceholder = null;

    protected ?string $searchDebounce = null;

    protected bool | Closure $isSearchOnBlur = false;

    protected bool | Closure $shouldSplitSearchTerms = true;

    protected ?Closure $searchUsing = null;

    public function persistSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsSearchInSession = $condition;

        return $this;
    }

    public function persistColumnSearchesInSession(bool | Closure $condition = true): static
    {
        $this->persistsColumnSearchesInSession = $condition;

        return $this;
    }

    /**
     * @param  bool | array<string | Closure>  $condition
     */
    public function searchable(bool | array $condition = true): static
    {
        if ($condition === true) {
            $this->isSearchable = true;
        } elseif (! $condition) {
            $this->isSearchable = false;
            $this->extraSearchableColumns = [];
        } else {
            $this->isSearchable = true;
            $this->extraSearchableColumns = $condition;
        }

        return $this;
    }

    public function searchDebounce(?string $debounce): static
    {
        $this->searchDebounce = $debounce;

        return $this;
    }

    public function isSearchable(): bool
    {
        if (is_bool($this->isSearchable)) {
            return $this->isSearchable;
        }

        if ($this->getExtraSearchableColumns()) {
            return true;
        }

        foreach ($this->getColumns() as $column) {
            if (! $column->isGloballySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function isSearchableByColumn(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isIndividuallySearchable()) {
                continue;
            }

            if ($column->isHidden()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function persistsSearchInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsSearchInSession);
    }

    public function persistsColumnSearchesInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsColumnSearchesInSession);
    }

    public function searchPlaceholder(string | Closure | null $searchPlaceholder): static
    {
        $this->searchPlaceholder = $searchPlaceholder;

        return $this;
    }

    public function getSearchPlaceholder(): ?string
    {
        return $this->evaluate($this->searchPlaceholder);
    }

    public function hasSearch(): bool
    {
        return $this->getLivewire()->hasTableSearch();
    }

    public function getSearchIndicator(): Indicator
    {
        return $this->getLivewire()->getTableSearchIndicator();
    }

    /**
     * @return array<Indicator> | array<string, string>
     */
    public function getColumnSearchIndicators(): array
    {
        return $this->getLivewire()->getTableColumnSearchIndicators();
    }

    public function getSearchDebounce(): string
    {
        return $this->searchDebounce ?? '500ms';
    }

    public function searchOnBlur(bool | Closure $condition = true): static
    {
        $this->isSearchOnBlur = $condition;

        return $this;
    }

    public function isSearchOnBlur(): bool
    {
        return (bool) $this->evaluate($this->isSearchOnBlur);
    }

    /**
     * @return array<string | Closure>
     */
    public function getExtraSearchableColumns(): array
    {
        return $this->extraSearchableColumns;
    }

    public function applyExtraSearchConstraints(Builder $query, string $search, bool &$isFirst): void
    {
        foreach ($this->getExtraSearchableColumns() as $column) {
            if (blank($column)) {
                continue;
            }

            $whereClause = $isFirst ? 'where' : 'orWhere';

            if ($column instanceof Closure) {
                $query->{$whereClause}(
                    fn ($query) => $this->evaluate($column, [
                        'query' => $query,
                        'search' => $search,
                        'searchQuery' => $search,
                    ]),
                );

                $isFirst = false;

                continue;
            }

            $model = $query->getModel();

            $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

            $query->when(
                $translatableContentDriver?->isAttributeTranslatable($model::class, attribute: $column),
                fn (Builder $query): Builder => $translatableContentDriver->applySearchConstraintToQuery($query, $column, $search, $whereClause),
                fn (Builder $query) => $query->when(
                    $this->getExtraSearchableColumnRelationship($column, $query->getModel()),
                    fn (Builder $query): Builder => $query->{"{$whereClause}Has"}(
                        (string) str($column)->beforeLast('.'),
                        fn (Builder $query): Builder => apply_search_constraint(
                            $query,
                            (string) str($column)->afterLast('.'),
                            "%{$search}%",
                        ),
                    ),
                    function (Builder $query) use ($column, $search, $whereClause): Builder {
                        // Treat the missing "relationship" as a JSON column if dot notation is used in the column name.
                        if (str($column)->contains('.')) {
                            $column = (string) str($column)->replace('.', '->');
                        }

                        return apply_search_constraint(
                            $query,
                            $column,
                            "%{$search}%",
                            boolean: ($whereClause === 'where') ? 'and' : 'or',
                        );
                    },
                ),
            );

            $isFirst = false;
        }
    }

    public function getExtraSearchableColumnRelationship(string $name, Model $record): ?Relation
    {
        if (blank($name) || (! str($name)->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (str($name)->beforeLast('.')->explode('.')->all() as $nestedRelationshipName) {
            if ($record->hasAttribute($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    public function splitSearchTerms(bool | Closure $condition = true): static
    {
        $this->shouldSplitSearchTerms = $condition;

        return $this;
    }

    public function shouldSplitSearchTerms(): bool
    {
        return (bool) $this->evaluate($this->shouldSplitSearchTerms);
    }

    public function searchUsing(?Closure $searchUsing): static
    {
        $this->searchUsing = $searchUsing;

        return $this;
    }

    public function hasSearchUsingCallback(): bool
    {
        return filled($this->searchUsing);
    }

    public function callSearchUsing(Builder $query, string $search): void
    {
        $this->evaluate($this->searchUsing, [
            'query' => $query,
            'search' => $search,
        ]);
    }
}
