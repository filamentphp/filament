<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Znck\Eloquent\Relations\BelongsToThrough;

use function Filament\Support\generate_search_term_expression;

class SelectFilter extends BaseFilter
{
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use Concerns\HasRelationship;

    protected string | Closure | null $attribute = null;

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isNative = true;

    protected bool | Closure $isStatic = false;

    /**
     * @var bool | array<string> | Closure
     */
    protected bool | array | Closure $searchable = false;

    protected bool | Closure $canSelectPlaceholder = true;

    protected int | Closure $optionsLimit = 50;

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected const EMPTY_RELATIONSHIP_OPTION_KEY = '__empty';

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(
            fn (SelectFilter $filter): string => $filter->isMultiple() ?
                __('filament-tables::table.filters.multi_select.placeholder') :
                __('filament-tables::table.filters.select.placeholder'),
        );

        $this->indicateUsing(static function (SelectFilter $filter, array $state): array {
            if ($filter->isMultiple()) {
                if (blank($state['values'] ?? null)) {
                    return [];
                }

                if ($filter->queriesRelationships()) {
                    $relationshipQuery = $filter->getRelationshipQuery();

                    $labels = [];

                    if (
                        $filter->hasEmptyRelationshipOption() &&
                        in_array(static::EMPTY_RELATIONSHIP_OPTION_KEY, $state['values'])
                    ) {
                        $labels[] = $filter->getEmptyRelationshipOptionLabel();
                    }

                    $labels = [
                        ...$labels,
                        ...$relationshipQuery
                            ->when(
                                $filter->getRelationship() instanceof BelongsToThrough,
                                fn (Builder $query) => $query->distinct(),
                            )
                            ->when(
                                $filter->getRelationshipKey(),
                                fn (Builder $query, string $relationshipKey) => $query->whereIn($relationshipKey, $filter->getRelationshipQueryValues($state['values'])),
                                fn (Builder $query) => $query->whereKey($state['values'])
                            )
                            ->pluck($relationshipQuery->qualifyColumn($filter->getRelationshipTitleAttribute()))
                            ->all(),
                    ];
                } else {
                    $labels = collect($filter->getOptions())
                        ->mapWithKeys(fn (string | array $label, string $value): array => is_array($label) ? $label : [$value => $label])
                        ->only($state['values'])
                        ->all();
                }

                if (! count($labels)) {
                    return [];
                }

                $labels = collect($labels)->join(', ', ' & ');

                $indicator = $filter->getIndicator();

                if (! $indicator instanceof Indicator) {
                    $indicator = Indicator::make("{$indicator}: {$labels}");
                }

                return [$indicator];
            }

            if (blank($state['value'] ?? null)) {
                return [];
            }

            if ($filter->queriesRelationships()) {
                if (
                    $filter->hasEmptyRelationshipOption() &&
                    ($state['value'] === static::EMPTY_RELATIONSHIP_OPTION_KEY)
                ) {
                    $label = $filter->getEmptyRelationshipOptionLabel();
                } else {
                    $label = $filter->getRelationshipQuery()
                        ->when(
                            $filter->getRelationshipKey(),
                            fn (Builder $query, string $relationshipKey) => $query->where($relationshipKey, $state['value']),
                            fn (Builder $query) => $query->whereKey($state['value'])
                        )
                        ->first()
                        ?->getAttributeValue($filter->getRelationshipTitleAttribute());
                }
            } else {
                $label = collect($filter->getOptions())
                    ->mapWithKeys(fn (string | array $label, string $value): array => is_array($label) ? $label : [$value => $label])
                    ->get($state['value']);
            }

            if (blank($label)) {
                return [];
            }

            $indicator = $filter->getIndicator();

            if (! $indicator instanceof Indicator) {
                $indicator = Indicator::make("{$indicator}: {$label}");
            }

            return [$indicator];
        });

        $this->resetState(['value' => null]);
    }

    public function getActiveCount(): int
    {
        $state = $this->getState();

        return filled($this->isMultiple() ? ($state['values'] ?? []) : ($state['value'] ?? null)) ? 1 : 0;
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $data
     * @return Builder<TModel>
     */
    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->evaluate($this->isStatic)) {
            return $query;
        }

        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        $isMultiple = $this->isMultiple();

        $values = $isMultiple ?
            $data['values'] ?? null :
            $data['value'] ?? null;

        if (blank(Arr::first(
            Arr::wrap($values),
            fn ($value): bool => filled($value),
        ))) {
            return $query;
        }

        if (! $this->queriesRelationships()) {
            return $query->{$isMultiple ? 'whereIn' : 'where'}(
                $query->qualifyColumn($this->getAttribute()),
                $values,
            );
        }

        $filteredValues = $this->getRelationshipQueryValues($values);

        $applyRelationshipScope = function (Builder $query) use ($isMultiple, $filteredValues): void {
            if (empty($filteredValues)) {
                return;
            }

            $query->whereHas(
                $this->getRelationshipName(),
                function (Builder $query) use ($isMultiple, $filteredValues): void {
                    if ($this->modifyRelationshipQueryUsing) {
                        $query = $this->evaluate($this->modifyRelationshipQueryUsing, [
                            'query' => $query,
                        ]) ?? $query;
                    }

                    $queryValues = $isMultiple ? $filteredValues : $filteredValues[0];

                    if ($relationshipKey = $this->getRelationshipKey($query)) {
                        $query->{$isMultiple ? 'whereIn' : 'where'}(
                            $relationshipKey,
                            $queryValues,
                        );

                        return;
                    }

                    $query->whereKey($queryValues);
                },
            );
        };

        if (
            $this->hasEmptyRelationshipOption() &&
            in_array(static::EMPTY_RELATIONSHIP_OPTION_KEY, Arr::wrap($values))
        ) {
            if (filled($filteredValues)) {
                $query
                    ->where(fn (Builder $query) => $applyRelationshipScope($query))
                    ->orWhereDoesntHave($this->getRelationshipName());
            } else {
                $query->whereDoesntHave($this->getRelationshipName());
            }
        } else {
            $applyRelationshipScope($query);
        }

        return $query;
    }

    public function attribute(string | Closure | null $name): static
    {
        $this->attribute = $name;

        return $this;
    }

    /**
     * @deprecated Use `attribute()` instead.
     */
    public function column(string | Closure | null $name): static
    {
        $this->attribute($name);

        return $this;
    }

    public function static(bool | Closure $condition = true): static
    {
        $this->isStatic = $condition;

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    /**
     * @param  bool | array<string> | Closure  $condition
     */
    public function searchable(bool | array | Closure $condition = true): static
    {
        $this->searchable = $condition;

        return $this;
    }

    public function selectablePlaceholder(bool | Closure $condition = true): static
    {
        $this->canSelectPlaceholder = $condition;

        return $this;
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    /**
     * @deprecated Use `getAttribute()` instead.
     */
    public function getColumn(): string
    {
        return $this->getAttribute();
    }

    public function forceSearchCaseInsensitive(bool | Closure | null $condition = true): static
    {
        $this->isSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    public function isSearchForcedCaseInsensitive(): ?bool
    {
        return $this->evaluate($this->isSearchForcedCaseInsensitive);
    }

    public function getFormField(): Select
    {
        $field = Select::make($this->isMultiple() ? 'values' : 'value')
            ->label($this->getLabel())
            ->multiple($this->isMultiple())
            ->placeholder($this->getPlaceholder())
            ->searchable($this->getSearchable())
            ->selectablePlaceholder($this->canSelectPlaceholder())
            ->preload($this->isPreloaded())
            ->native($this->isNative())
            ->optionsLimit($this->getOptionsLimit());

        if ($this->queriesRelationships()) {
            $field
                ->relationship(
                    $this->getRelationshipName(),
                    $this->getRelationshipTitleAttribute(),
                    $this->modifyRelationshipQueryUsing,
                )
                ->getSearchResultsUsing(function (Select $component, ?string $search): array {
                    $relationship = Relation::noConstraints(fn () => $component->getRelationship());

                    $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                    if ($this->modifyRelationshipQueryUsing) {
                        $relationshipQuery = $component->evaluate($this->modifyRelationshipQueryUsing, [
                            'query' => $relationshipQuery,
                            'search' => $search,
                        ]) ?? $relationshipQuery;
                    }

                    $component->applySearchConstraint(
                        $relationshipQuery,
                        generate_search_term_expression($search, $component->isSearchForcedCaseInsensitive(), $relationshipQuery->getConnection()),
                    );

                    $baseRelationshipQuery = $relationshipQuery->getQuery();

                    if (isset($baseRelationshipQuery->limit)) {
                        $component->optionsLimit($baseRelationshipQuery->limit);
                        $this->optionsLimit($baseRelationshipQuery->limit);
                    } else {
                        $relationshipQuery->limit($component->getOptionsLimit());
                    }

                    $options = [];

                    if (
                        $this->hasEmptyRelationshipOption() &&
                        str($this->getEmptyRelationshipOptionLabel())->lower()->contains(Str::lower($search))
                    ) {
                        $options[static::EMPTY_RELATIONSHIP_OPTION_KEY] = $this->getEmptyRelationshipOptionLabel();
                    }

                    $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

                    if ($component->hasOptionLabelFromRecordUsingCallback()) {
                        return $options + $relationshipQuery
                            ->get()
                            ->mapWithKeys(static fn (Model $record) => [
                                $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $component->getOptionLabelFromRecord($record),
                            ])
                            ->toArray();
                    }

                    $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

                    if (empty($relationshipQuery->getQuery()->orders)) {
                        $relationshipOrderByAttribute = $relationshipTitleAttribute;

                        if (str_contains($relationshipOrderByAttribute, ' as ')) {
                            $relationshipOrderByAttribute = (string) str($relationshipOrderByAttribute)->before(' as ');
                        }

                        $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($relationshipOrderByAttribute));
                    }

                    if (str_contains($relationshipTitleAttribute, '->')) {
                        if (! str_contains($relationshipTitleAttribute, ' as ')) {
                            $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                        }
                    } else {
                        $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
                    }

                    return $options + $relationshipQuery
                        ->pluck($relationshipTitleAttribute, $qualifiedRelatedKeyName)
                        ->toArray();
                })
                ->options(function (Select $component): ?array {
                    if (($component->isSearchable()) && ! $component->isPreloaded()) {
                        return null;
                    }

                    $relationship = Relation::noConstraints(fn () => $component->getRelationship());

                    $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                    if ($this->modifyRelationshipQueryUsing) {
                        $relationshipQuery = $component->evaluate($this->modifyRelationshipQueryUsing, [
                            'query' => $relationshipQuery,
                            'search' => null,
                        ]) ?? $relationshipQuery;
                    }

                    $baseRelationshipQuery = $relationshipQuery->getQuery();

                    if (isset($baseRelationshipQuery->limit)) {
                        $component->optionsLimit($baseRelationshipQuery->limit);
                        $this->optionsLimit($baseRelationshipQuery->limit);
                    } elseif ($component->isSearchable() && filled($component->getSearchColumns())) {
                        $relationshipQuery->limit($component->getOptionsLimit());
                    }

                    $options = [];

                    if ($this->hasEmptyRelationshipOption()) {
                        $options[static::EMPTY_RELATIONSHIP_OPTION_KEY] = $this->getEmptyRelationshipOptionLabel();
                    }

                    $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

                    if ($component->hasOptionLabelFromRecordUsingCallback()) {
                        return $options + $relationshipQuery
                            ->get()
                            ->mapWithKeys(static fn (Model $record) => [
                                $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $component->getOptionLabelFromRecord($record),
                            ])
                            ->toArray();
                    }

                    $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

                    if (empty($relationshipQuery->getQuery()->orders)) {
                        $relationshipOrderByAttribute = $relationshipTitleAttribute;

                        if (str_contains($relationshipOrderByAttribute, ' as ')) {
                            $relationshipOrderByAttribute = (string) str($relationshipOrderByAttribute)->before(' as ');
                        }

                        $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($relationshipOrderByAttribute));
                    }

                    if (str_contains($relationshipTitleAttribute, '->')) {
                        if (! str_contains($relationshipTitleAttribute, ' as ')) {
                            $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                        }
                    } else {
                        $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
                    }

                    return $options + $relationshipQuery
                        ->pluck($relationshipTitleAttribute, $qualifiedRelatedKeyName)
                        ->toArray();
                })
                ->getOptionLabelUsing(function (Select $component) {
                    if (
                        $this->hasEmptyRelationshipOption() &&
                        ($component->getState() === static::EMPTY_RELATIONSHIP_OPTION_KEY)
                    ) {
                        return $this->getEmptyRelationshipOptionLabel();
                    }

                    $record = $component->getSelectedRecord();

                    if (! $record) {
                        return null;
                    }

                    if ($component->hasOptionLabelFromRecordUsingCallback()) {
                        return $component->getOptionLabelFromRecord($record);
                    }

                    $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

                    if (str_contains($relationshipTitleAttribute, '->')) {
                        $relationshipTitleAttribute = str_replace('->', '.', $relationshipTitleAttribute);
                    }

                    return data_get($record, $relationshipTitleAttribute);
                })
                ->getOptionLabelsUsing(function (Select $component, array $values): array {
                    $relationship = Relation::noConstraints(fn () => $component->getRelationship());

                    $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                    $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

                    $relationshipQuery->whereIn($qualifiedRelatedKeyName, $this->getRelationshipQueryValues($values));

                    if ($this->modifyRelationshipQueryUsing) {
                        $relationshipQuery = $component->evaluate($this->modifyRelationshipQueryUsing, [
                            'query' => $relationshipQuery,
                            'search' => null,
                        ]) ?? $relationshipQuery;
                    }

                    $labels = [];

                    if (
                        $this->hasEmptyRelationshipOption() &&
                        in_array(static::EMPTY_RELATIONSHIP_OPTION_KEY, $values)
                    ) {
                        $labels[static::EMPTY_RELATIONSHIP_OPTION_KEY] = $this->getEmptyRelationshipOptionLabel();
                    }

                    if ($component->hasOptionLabelFromRecordUsingCallback()) {
                        return $labels + $relationshipQuery
                            ->get()
                            ->mapWithKeys(static fn (Model $record) => [
                                $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $component->getOptionLabelFromRecord($record),
                            ])
                            ->toArray();
                    }

                    $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

                    if (str_contains($relationshipTitleAttribute, '->')) {
                        if (! str_contains($relationshipTitleAttribute, ' as ')) {
                            $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                        }
                    } else {
                        $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
                    }

                    return $labels + $relationshipQuery
                        ->pluck($relationshipTitleAttribute, $qualifiedRelatedKeyName)
                        ->toArray();
                })
                ->forceSearchCaseInsensitive($this->isSearchForcedCaseInsensitive());
        } else {
            $field->options(fn (): array => $this->getOptions());
        }

        if ($this->getOptionLabelUsing) {
            $field->getOptionLabelUsing($this->getOptionLabelUsing);
        }

        if ($this->getOptionLabelsUsing) {
            $field->getOptionLabelsUsing($this->getOptionLabelsUsing);
        }

        if ($this->getOptionLabelFromRecordUsing) {
            $field->getOptionLabelFromRecordUsing($this->getOptionLabelFromRecordUsing);
        }

        if ($this->getSearchResultsUsing) {
            $field->getSearchResultsUsing($this->getSearchResultsUsing);
        }

        if (filled($defaultState = $this->getDefaultState())) {
            $field->default($defaultState);
        }

        return $field;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    /**
     * @return bool | array<string> | Closure
     */
    public function getSearchable(): bool | array | Closure
    {
        return $this->evaluate($this->searchable);
    }

    public function canSelectPlaceholder(): bool
    {
        return (bool) $this->evaluate($this->canSelectPlaceholder);
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function native(bool | Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    /**
     * @param  string | array<string>  $values
     * @return array<string>
     */
    protected function getRelationshipQueryValues(string | array $values): array
    {
        return array_values(array_filter(
            Arr::wrap($values),
            fn (string $value): bool => $value !== static::EMPTY_RELATIONSHIP_OPTION_KEY,
        ));
    }
}
