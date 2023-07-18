<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanAggregateRelatedModels
{
    protected string | Closure | null $columnToAvg = null;

    protected string | Closure | null $relationshipToAvg = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $relationshipsToCount = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $relationshipsToExistenceCheck = null;

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

    /**
     * @param  string | array<string> | Closure | null  $relationships
     */
    public function counts(string | array | Closure | null $relationships): static
    {
        $this->relationshipsToCount = $relationships;

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $relationships
     */
    public function exists(string | array | Closure | null $relationships): static
    {
        $this->relationshipsToExistenceCheck = $relationships;

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

    /**
     * @return string | array<string> | null
     */
    public function getRelationshipsToCount(): string | array | null
    {
        return $this->evaluate($this->relationshipsToCount);
    }

    /**
     * @return string | array<string> | null
     */
    public function getRelationshipsToExistenceCheck(): string | array | null
    {
        return $this->evaluate($this->relationshipsToExistenceCheck);
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
