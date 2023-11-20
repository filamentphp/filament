<?php

namespace Filament\Tables\Filters\QueryBuilder\Concerns;

use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;

trait HasConstraints
{
    /** @var array<Constraint> */
    protected array $constraints = [];

    /**
     * @param  array<Constraint>  $constraints
     */
    public function constraints(array $constraints): static
    {
        foreach ($constraints as $constraint) {
            if ($this instanceof QueryBuilder) {
                $constraint->filter($this);
            }

            $this->constraints[$constraint->getName()] = $constraint;
        }

        return $this;
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->evaluate($this->constraints);
    }

    public function getConstraint(string $name): ?Constraint
    {
        return $this->getConstraints()[$name] ?? null;
    }
}
