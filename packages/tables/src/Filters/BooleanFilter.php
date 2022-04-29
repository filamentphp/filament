<?php

namespace Filament\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class BooleanFilter extends SelectFilter
{
    protected string | Closure | null $trueLabel = null;

    protected string | Closure | null $falseLabel = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(fn () => [
            'yes' => $this->getTrueLabel(),
            'no' => $this->getFalseLabel(),
        ]);
    }

    public function getTrueLabel(): string
    {
        return $this->evaluate($this->trueLabel) ?? __('tables::table.filters.boolean.true');
    }

    public function getFalseLabel(): string
    {
        return $this->evaluate($this->falseLabel) ?? __('tables::table.filters.boolean.false');
    }

    public function trueLabel(string | Closure | null $trueLabel): static
    {
        $this->trueLabel = $trueLabel;

        return $this;
    }

    public function falseLabel(string | Closure | null $falseLabel): static
    {
        $this->falseLabel = $falseLabel;

        return $this;
    }

    public function nullableAttribute(string $attribute): static
    {
        return $this->conditions(
            fn (Builder $query) => $query->whereNotNull($attribute),
            fn (Builder $query) => $query->whereNull($attribute),
        );
    }

    public function booleanAttribute(string $attribute): static
    {
        return $this->conditions(
            fn (Builder $query) => $query->where($attribute, true),
            fn (Builder $query) => $query->where($attribute, false),
        );
    }

    public function conditions(Closure $trueQuery, Closure $falseQuery, Closure $blankQuery = null): static
    {
        return $this->query(function (Builder $query, array $data) use ($trueQuery, $falseQuery, $blankQuery) {
            if (blank($data['value'])) {
                return $blankQuery instanceof Closure
                    ? $blankQuery($query)
                    : $query;
            }

            if ($data['value'] == 'yes') {
                return $trueQuery($query);
            }

            return $falseQuery($query);
        });
    }
}
