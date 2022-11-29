<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanAggregateRelatedModels
{
    protected string | Closure | null $columnToAvg = null;

    protected string | Closure | null $relationshipToAvg = null;

    protected string | Closure | null $relationshipToCount = null;

    protected string | Closure | null $relationshipToExistenceCheck = null;

    protected string | Closure | null $columnToMax = null;

    protected string | Closure | null $relationshipToMax = null;

    protected string | Closure | null $columnToMin = null;

    protected string | Closure | null $relationshipToMin = null;

    protected string | Closure | null $columnToSum = null;

    protected string | Closure | null $relationshipToSum = null;

    public function avg(string | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToAvg = $column;
        $this->relationshipToAvg = $relationship;

        return $this;
    }

    public function counts(string | Closure | null $relationship): static
    {
        $this->relationshipToCount = $relationship;

        return $this;
    }

    public function exists(string | Closure | null $relationship): static
    {
        $this->relationshipToExistenceCheck = $relationship;

        return $this;
    }

    public function max(string | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToMax = $column;
        $this->relationshipToMax = $relationship;

        return $this;
    }

    public function min(string | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToMin = $column;
        $this->relationshipToMin = $relationship;

        return $this;
    }

    public function sum(string | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToSum = $column;
        $this->relationshipToSum = $relationship;

        return $this;
    }

    public function getColumnToAvg(): ?string
    {
        return $this->evaluate($this->columnToAvg);
    }

    public function getRelationshipToAvg(): ?string
    {
        return $this->evaluate($this->relationshipToAvg);
    }

    public function getRelationshipToCount(): ?string
    {
        return $this->evaluate($this->relationshipToCount);
    }

    public function getRelationshipToExistenceCheck(): ?string
    {
        return $this->evaluate($this->relationshipToExistenceCheck);
    }

    public function getColumnToMax(): ?string
    {
        return $this->evaluate($this->columnToMax);
    }

    public function getRelationshipToMax(): ?string
    {
        return $this->evaluate($this->relationshipToMax);
    }

    public function getColumnToMin(): ?string
    {
        return $this->evaluate($this->columnToMin);
    }

    public function getRelationshipToMin(): ?string
    {
        return $this->evaluate($this->relationshipToMin);
    }

    public function getColumnToSum(): ?string
    {
        return $this->evaluate($this->columnToSum);
    }

    public function getRelationshipToSum(): ?string
    {
        return $this->evaluate($this->relationshipToSum);
    }
}
