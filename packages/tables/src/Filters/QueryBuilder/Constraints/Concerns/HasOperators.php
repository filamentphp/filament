<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\Concerns;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

trait HasOperators
{
    /** @var array<Operator> */
    protected array $operators = [];

    /**
     * @param array<class-string<Operator> | Operator> $operators
     */
    public function operators(array $operators): static
    {
        foreach ($operators as $operator) {
            if (is_string($operator)) {
                $operator = $operator::make();
            }

            $this->operators[$operator->getName()] = $operator;
        }

        return $this;
    }

    /**
     * @return array<Operator>
     */
    public function getOperators(): array
    {
        return array_filter(
            $this->operators,
            fn (Operator $operator): bool => $operator->isVisible(),
        );
    }

    public function getOperator(string $name): ?Operator
    {
        return $this->getOperators()[$name] ?? null;
    }
}
