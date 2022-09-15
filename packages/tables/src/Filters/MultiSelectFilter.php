<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class MultiSelectFilter extends BaseFilter
{
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use Concerns\HasRelationship;

    protected string | Closure | null $attribute = null;

    protected bool | Closure $isStatic = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('tables::table.filters.multi_select.placeholder'));

        $this->indicateUsing(function (array $state): array {
            if (blank($state['values'] ?? null)) {
                return [];
            }

            $labels = Arr::only($this->getOptions(), $state['values']);

            if (! count($labels)) {
                return [];
            }

            $labels = collect($labels)->join(', ', ' & ');

            return ["{$this->getIndicator()}: {$labels}"];
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

        if (blank($data['values'] ?? null)) {
            return $query;
        }

        if ($this->queriesRelationships()) {
            return $query->whereHas(
                $this->getRelationshipName(),
                fn (Builder $query) => $query->whereIn(
                    $this->getRelationshipKey(),
                    $data['values'],
                ),
            );
        }

        /** @var Builder $query */
        $query = $query->whereIn($this->getAttribute(), $data['values']);

        return $query;
    }

    /** @deprecated use `->attribute()` on the filter instead */
    public function column(string | Closure | null $name): static
    {
        $this->attribute($name);

        return $this;
    }

    public function attribute(string | Closure | null $name): static
    {
        $this->attribute = $name;

        return $this;
    }

    public function static(bool | Closure $condition = true): static
    {
        $this->isStatic = $condition;

        return $this;
    }

    /** @deprecated use `->getAttribute()` instead */
    public function getColumn(): string
    {
        return $this->getAttribute();
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    protected function getFormField(): Select
    {
        $field = MultiSelect::make('values')
            ->label($this->getLabel())
            ->options($this->getOptions())
            ->placeholder($this->getPlaceholder())
            ->default($this->getDefaultState())
            ->columnSpan($this->getColumnSpan());

        if (filled($defaultState = $this->getDefaultState())) {
            $field->default($defaultState);
        }

        return $field;
    }
}
