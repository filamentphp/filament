<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

use function Livewire\invade;

trait HasQuery
{
    protected Builder | Closure | null $query = null;

    protected string | Closure | null $inverseRelationship = null;

    protected ?Closure $getRelationshipUsing = null;

    /**
     * @var array<Closure>
     */
    protected array $queryScopes = [];

    public function query(Builder | Closure | null $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function modifyQueryUsing(Closure $callback): static
    {
        $this->queryScopes[] = $callback;

        return $this;
    }

    public function relationship(?Closure $relationship): static
    {
        $this->getRelationshipUsing = $relationship;

        return $this;
    }

    public function inverseRelationship(string | Closure | null $name): static
    {
        $this->inverseRelationship = $name;

        return $this;
    }

    protected function applyQueryScopes(Builder $query): Builder
    {
        foreach ($this->queryScopes as $scope) {
            $query = $this->evaluate($scope, ['query' => $query]) ?? $query;
        }

        return $query;
    }

    public function getQuery(): Builder | Relation
    {
        if ($query = $this->evaluate($this->query)) {
            return $this->applyQueryScopes($query->clone());
        }

        if ($query = $this->getRelationshipQuery()) {
            return $this->applyQueryScopes($query->clone());
        }

        $livewireClass = $this->getLivewire()::class;

        throw new Exception("Table [{$livewireClass}] must have a [query()].");
    }

    public function getRelationshipQuery(): ?Builder
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return null;
        }

        $query = $relationship->getQuery();

        if ($relationship instanceof HasManyThrough) {
            // https://github.com/laravel/framework/issues/4962
            $query->select($query->getModel()->getTable() . '.*');

            return $query;
        }

        if ($relationship instanceof BelongsToMany) {
            // https://github.com/laravel/framework/issues/4962
            if (! $this->allowsDuplicates()) {
                $this->selectPivotDataInQuery($query);
            }

            // https://github.com/filamentphp/filament/issues/2079
            $query->withCasts(
                app($relationship->getPivotClass())->getCasts(),
            );
        }

        return $query;
    }

    public function selectPivotDataInQuery(Builder | Relation $query): Builder | Relation
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $columns = [
            $relationship->getTable() . '.*',
            $query->getModel()->getTable() . '.*',
        ];

        if (! $this->allowsDuplicates()) {
            $columns = [
                ...invade($relationship)->aliasedPivotColumns(),
                ...$columns,
            ];
        }

        $query->select($columns);

        return $query;
    }

    public function getRelationship(): Relation | Builder | null
    {
        return $this->evaluate($this->getRelationshipUsing);
    }

    public function getInverseRelationship(): ?string
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return null;
        }

        return $this->evaluate($this->inverseRelationship) ?? (string) str(class_basename($relationship->getParent()::class))
            ->plural()
            ->camel();
    }

    public function getInverseRelationshipFor(Model $record): Relation | Builder
    {
        return $record->{$this->getInverseRelationship()}();
    }
}
