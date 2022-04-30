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

        $this->placeholder(__('tables::table.filters.ternary.placeholder'));

        $this->booleanColumn();
    }

    public function getTrueLabel(): string
    {
        return $this->evaluate($this->trueLabel) ?? __('tables::table.filters.ternary.true');
    }

    public function getFalseLabel(): string
    {
        return $this->evaluate($this->falseLabel) ?? __('tables::table.filters.ternary.false');
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

    public function nullableColumn(string | Closure | null $name = null): static
    {
        return $this->column($name)->conditions(
            fn (Builder $query) => $query->whereNotNull($this->getColumn()),
            fn (Builder $query) => $query->whereNull($this->getColumn()),
        );
    }

    public function booleanColumn(string | Closure | null $name = null): static
    {
        return $this->column($name)->conditions(
            fn (Builder $query) => $query->where($this->getColumn(), true),
            fn (Builder $query) => $query->where($this->getColumn(), false),
        );
    }

    public function conditions(Closure $trueQuery, Closure $falseQuery, Closure $blankQuery = null): static
    {
        return $this->query(function (Builder $query, array $data) use ($trueQuery, $falseQuery, $blankQuery) {
            if (blank($data['value'] ?? null)) {
                return $blankQuery instanceof Closure
                    ? $blankQuery($query, $data)
                    : $query;
            }

            if ($data['value'] == 'yes') {
                return $trueQuery($query, $data);
            }

            return $falseQuery($query, $data);
        });
    }
}
