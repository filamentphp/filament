<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Znck\Eloquent\Relations\BelongsToThrough;

class SelectFilter extends BaseFilter
{
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use Concerns\HasRelationship;

    protected string | Closure | null $attribute = null;

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isNative = true;

    protected bool | Closure $isStatic = false;

    protected bool | Closure $isSearchable = false;

    protected bool | Closure $canSelectPlaceholder = true;

    protected int | Closure $optionsLimit = 50;

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(
            fn (SelectFilter $filter): string => $filter->isMultiple() ?
                __('filament-tables::table.filters.multi_select.placeholder') :
                __('filament-tables::table.filters.select.placeholder'),
        );

        $this->indicateUsing(function (SelectFilter $filter, array $state): array {
            if ($filter->isMultiple()) {
                if (blank($state['values'] ?? null)) {
                    return [];
                }

                if ($filter->queriesRelationships()) {
                    $relationshipQuery = $filter->getRelationshipQuery();

                    $labels = $relationshipQuery
                        ->when(
                            $filter->getRelationship() instanceof BelongsToThrough,
                            fn (Builder $query) => $query->distinct(),
                        )
                        ->when(
                            $this->getRelationshipKey(),
                            fn (Builder $query, string $relationshipKey) => $query->whereIn($relationshipKey, $state['values']),
                            fn (Builder $query) => $query->whereKey($state['values'])
                        )
                        ->pluck($relationshipQuery->qualifyColumn($filter->getRelationshipTitleAttribute()))
                        ->all();
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
                $label = $filter->getRelationshipQuery()
                    ->when(
                        $this->getRelationshipKey(),
                        fn (Builder $query, string $relationshipKey) => $query->where($relationshipKey, $state['value']),
                        fn (Builder $query) => $query->whereKey($state['value'])
                    )
                    ->first()
                    ?->getAttributeValue($filter->getRelationshipTitleAttribute());
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
     * @param  array<string, mixed>  $data
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
            fn ($value) => filled($value),
        ))) {
            return $query;
        }

        if (! $this->queriesRelationships()) {
            return $query->{$isMultiple ? 'whereIn' : 'where'}(
                $this->getAttribute(),
                $values,
            );
        }

        return $query->whereHas(
            $this->getRelationshipName(),
            function (Builder $query) use ($isMultiple, $values) {
                if ($this->modifyRelationshipQueryUsing) {
                    $query = $this->evaluate($this->modifyRelationshipQueryUsing, [
                        'query' => $query,
                    ]) ?? $query;
                }

                if ($relationshipKey = $this->getRelationshipKey($query)) {
                    return $query->{$isMultiple ? 'whereIn' : 'where'}(
                        $relationshipKey,
                        $values,
                    );
                }

                return $query->whereKey($values);
            },
        );
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

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

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
            ->searchable($this->isSearchable())
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
                ->forceSearchCaseInsensitive($this->isSearchForcedCaseInsensitive());
        } else {
            $field->options($this->getOptions());
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

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
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
}
