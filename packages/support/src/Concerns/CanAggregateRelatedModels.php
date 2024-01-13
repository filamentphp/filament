<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanAggregateRelatedModels
{
    protected string | Closure | null $columnToAvg = null;

    /**
     * @var string | array<int | string, string | Closure> | Closure | null
     */
    protected string | array | Closure | null $relationshipToAvg = null;

    /**
     * @var string | array<int | string, string | Closure> | Closure | null
     */
    protected string | array | Closure | null $relationshipsToCount = null;

    /**
     * @var string | array<int | string, string | Closure> | Closure | null
     */
    protected string | array | Closure | null $relationshipsToExistenceCheck = null;

    protected string | Closure | null $columnToMax = null;

    /**
     * @var string | array<int | string, string | Closure> | Closure | null
     */
    protected string | array | Closure | null $relationshipToMax = null;

    protected string | Closure | null $columnToMin = null;

    /**
     * @var string | array<int | string, string | Closure> | Closure | null
     */
    protected string | array | Closure | null $relationshipToMin = null;

    protected string | Closure | null $columnToSum = null;

    /**
     * @var string | array<int | string, string | Closure> | Closure | null
     */
    protected string | array | Closure | null $relationshipToSum = null;

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function avg(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToAvg = $column;
        $this->relationshipToAvg = $relationship;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationships
     */
    public function counts(string | array | Closure | null $relationships): static
    {
        $this->relationshipsToCount = $relationships;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationships
     */
    public function exists(string | array | Closure | null $relationships): static
    {
        $this->relationshipsToExistenceCheck = $relationships;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function max(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToMax = $column;
        $this->relationshipToMax = $relationship;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function min(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToMin = $column;
        $this->relationshipToMin = $relationship;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function sum(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->columnToSum = $column;
        $this->relationshipToSum = $relationship;

        return $this;
    }

    public function getColumnToAvg(): ?string
    {
        return $this->evaluate($this->columnToAvg);
    }

    /**
     * @return string | array<int | string, string | Closure> | null
     */
    public function getRelationshipToAvg(): string | array | null
    {
        return $this->evaluate($this->relationshipToAvg);
    }

    /**
     * @return string | array<int | string, string | Closure> | null
     */
    public function getRelationshipsToCount(): string | array | null
    {
        return $this->evaluate($this->relationshipsToCount);
    }

    /**
     * @return string | array<int | string, string | Closure> | null
     */
    public function getRelationshipsToExistenceCheck(): string | array | null
    {
        return $this->evaluate($this->relationshipsToExistenceCheck);
    }

    public function getColumnToMax(): ?string
    {
        return $this->evaluate($this->columnToMax);
    }

    /**
     * @return string | array<int | string, string | Closure> | null
     */
    public function getRelationshipToMax(): string | array | null
    {
        return $this->evaluate($this->relationshipToMax);
    }

    public function getColumnToMin(): ?string
    {
        return $this->evaluate($this->columnToMin);
    }

    /**
     * @return string | array<int | string, string | Closure> | null
     */
    public function getRelationshipToMin(): string | array | null
    {
        return $this->evaluate($this->relationshipToMin);
    }

    public function getColumnToSum(): ?string
    {
        return $this->evaluate($this->columnToSum);
    }

    /**
     * @return string | array<int | string, string | Closure> | null
     */
    public function getRelationshipToSum(): string | array | null
    {
        return $this->evaluate($this->relationshipToSum);
    }
}
