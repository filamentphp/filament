<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class SelectFilter extends BaseFilter
{
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use Concerns\HasRelationship;

    protected string | Closure | null $column = null;

    protected bool | Closure $isStatic = false;

    protected bool | Closure $isSearchable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('tables::table.filters.select.placeholder'));

        $this->indicateUsing(function (array $state): array {
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

        if (blank($data['value'] ?? null)) {
            return $query;
        }

        if ($this->queriesRelationships()) {
            return $query->whereRelation(
                $this->getRelationshipName(),
                $this->getRelationshipKey(),
                $data['value'],
            );
        }

        return $query->where($this->getColumn(), $data['value']);
    }

    public function column(string | Closure | null $name): static
    {
        $this->column = $name;

        return $this;
    }

    public function static(bool | Closure $condition = true): static
    {
        $this->isStatic = $condition;

        return $this;
    }

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->evaluate($this->column) ?? $this->getName();
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
        $field = Select::make('value')
            ->label($this->getLabel())
            ->options($this->getOptions())
            ->placeholder($this->getPlaceholder())
            ->searchable($this->isSearchable())
            ->columnSpan($this->getColumnSpan());

        if (filled($defaultState = $this->getDefaultState())) {
            $field->default($defaultState);
        }

        return $field;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }
}
