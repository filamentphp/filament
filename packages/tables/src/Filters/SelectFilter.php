<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class SelectFilter extends BaseFilter
{
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use Concerns\HasRelationship;

    protected string | Closure | null $attribute = null;

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isStatic = false;

    protected bool | Closure $isSearchable = false;

    protected int | Closure $optionsLimit = 50;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(
            fn (SelectFilter $filter): string => $filter->isMultiple() ?
                __('tables::table.filters.multi_select.placeholder') :
                __('tables::table.filters.select.placeholder'),
        );

        $this->indicateUsing(function (SelectFilter $filter, array $state): array {
            if ($filter->isMultiple()) {
                if (blank($state['values'] ?? null)) {
                    return [];
                }

                $labels = Arr::only($this->getOptions(), $state['values']);

                if (! count($labels)) {
                    return [];
                }

                $labels = collect($labels)->join(', ', ' & ');

                return ["{$this->getIndicator()}: {$labels}"];
            }

            if (blank($state['value'] ?? null)) {
                return [];
            }

            $label = $this->getOptions()[$state['value']] ?? null;

            if (blank($label)) {
                return [];
            }

            return ["{$this->getIndicator()}: {$label}"];
        });
    }

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

        if (! count(array_filter(
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

        if ($isMultiple) {
            return $query->whereHas(
                $this->getRelationshipName(),
                fn (Builder $query) => $query->whereIn(
                    $this->getRelationshipKey(),
                    $values,
                ),
            );
        }

        return $query->whereRelation(
            $this->getRelationshipName(),
            $this->getRelationshipKey(),
            $values,
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

    protected function getFormField(): Select
    {
        return $this->getFormSelectComponent();
    }

    /**
     * @deprecated Overwrite `getFormField()` instead.
     */
    protected function getFormSelectComponent(): Select
    {
        $field = Select::make($this->isMultiple() ? 'values' : 'value')
            ->multiple($this->isMultiple())
            ->label($this->getLabel())
            ->options($this->getOptions())
            ->placeholder($this->getPlaceholder())
            ->searchable($this->isSearchable())
            ->optionsLimit($this->getOptionsLimit());

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

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }
}
