<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class TernaryFilter extends SelectFilter
{
    protected string | Closure | null $trueLabel = null;

    protected string | Closure | null $falseLabel = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trueLabel(__('forms::components.select.boolean.true'));
        $this->falseLabel(__('forms::components.select.boolean.false'));
        $this->placeholder('-');

        $this->boolean();

        $this->indicateUsing(function (array $state): array {
            if ($state['value'] ?? null) {
                return [$this->getTrueLabel()];
            }

            if (blank($state['value'] ?? null)) {
                return [];
            }

            return [$this->getFalseLabel()];
        });
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

    public function getTrueLabel(): ?string
    {
        return $this->trueLabel;
    }

    public function getFalseLabel(): ?string
    {
        return $this->falseLabel;
    }

    protected function getFormField(): Select
    {
        return parent::getFormField()
            ->boolean(
                trueLabel: $this->getTrueLabel(),
                falseLabel: $this->getFalseLabel(),
                placeholder: $this->getPlaceholder(),
            );
    }

    public function nullable(): static
    {
        $this->queries(
            true: function (Builder $query): Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleColumnName(),
                        '!=',
                        null,
                    );
                }

                return $query->whereNotNull($this->getColumn());
            },
            false: function (Builder $query): Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleColumnName(),
                        null,
                    );
                }

                return $query->whereNull($this->getColumn());
            },
        );

        return $this;
    }

    public function boolean(): static
    {
        $this->queries(
            true: function (Builder $query): Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleColumnName(),
                        true,
                    );
                }

                return $query->where($this->getColumn(), true);
            },
            false: function (Builder $query): Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleColumnName(),
                        false,
                    );
                }

                return $query->where($this->getColumn(), false);
            },
        );

        return $this;
    }

    public function queries(Closure $true, Closure $false, Closure $blank = null): static
    {
        $this->query(function (Builder $query, array $data) use ($blank, $false, $true) {
            if (blank($data['value'] ?? null)) {
                return $blank instanceof Closure
                    ? $blank($query, $data)
                    : $query;
            }

            return $data['value'] ? $true($query, $data) : $false($query, $data);
        });

        return $this;
    }
}
