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

        $this->trueLabel(__('filament-forms::components.select.boolean.true'));
        $this->falseLabel(__('filament-forms::components.select.boolean.false'));
        $this->placeholder('-');

        $this->boolean();

        $this->indicateUsing(function (TernaryFilter $filter, array $state): array {
            if (blank($state['value'] ?? null)) {
                return [];
            }

            $stateLabel = $state['value'] ?
                $filter->getTrueLabel() :
                $filter->getFalseLabel();

            $indicator = $filter->getIndicator();

            if (! $indicator instanceof Indicator) {
                $indicator = Indicator::make("{$indicator}: {$stateLabel}");
            }

            return [$indicator];
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
        return $this->evaluate($this->trueLabel);
    }

    public function getFalseLabel(): ?string
    {
        return $this->evaluate($this->falseLabel);
    }

    public function getFormField(): Select
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
                        $this->getRelationshipTitleAttribute(),
                        '!=',
                        null,
                    );
                }

                return $query->whereNotNull($this->getAttribute());
            },
            false: function (Builder $query): Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleAttribute(),
                        null,
                    );
                }

                return $query->whereNull($this->getAttribute());
            },
        );

        return $this;
    }

    public function boolean(bool $isNumeric = false) : static
    {
        $this->queries(
            true: function (Builder $query) use ($isNumeric) : Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleAttribute(),
                        $isNumeric ? 1 : true,
                    );
                }

                return $query->where($this->getAttribute(), $isNumeric ? 1 : true);
            },
            false: function (Builder $query) use ($isNumeric) : Builder {
                if ($this->queriesRelationships()) {
                    return $query->whereRelation(
                        $this->getRelationshipName(),
                        $this->getRelationshipTitleAttribute(),
                        $isNumeric ? 0 : false,
                    );
                }

                return $query->where($this->getAttribute(), $isNumeric ? 0 : false);
            },
        );

        return $this;
    }

    public function queries(Closure $true, Closure $false, ?Closure $blank = null): static
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

    public function getDefaultState(): mixed
    {
        $defaultState = $this->evaluate($this->defaultState);

        // Ensure that the default state is cast to an integer
        // so that it matches the value of a select option.
        if (is_bool($defaultState)) {
            $defaultState = $defaultState ? 1 : 0;
        }

        return $defaultState;
    }
}
