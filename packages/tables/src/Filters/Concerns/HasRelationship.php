<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasRelationship
{
    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected bool | Closure $isPreloaded = false;

    public function relationship(string $name, string $titleAttribute, ?Closure $modifyQueryUsing = null): static
    {
        $this->attribute("{$name}.{$titleAttribute}");

        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;

        return $this;
    }

    public function preload(bool | Closure $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function isPreloaded(): bool
    {
        return (bool) $this->evaluate($this->isPreloaded);
    }

    public function queriesRelationships(): bool
    {
        return str($this->getAttribute())->contains('.');
    }

    public function getRelationship(): Relation | Builder
    {
        $record = app($this->getTable()->getModel());

        $relationship = null;

        foreach (explode('.', $this->getRelationshipName()) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    public function getRelationshipName(): string
    {
        return (string) str($this->getAttribute())->beforeLast('.');
    }

    public function getRelationshipTitleAttribute(): string
    {
        return (string) str($this->getAttribute())->afterLast('.');
    }

    public function getModifyRelationshipQueryUsing(): ?Closure
    {
        return $this->modifyRelationshipQueryUsing;
    }

    public function getRelationshipQuery(): ?Builder
    {
        $relationship = Relation::noConstraints(fn () => $this->getRelationship());

        $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

        if ($this->getModifyRelationshipQueryUsing()) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if (empty($relationshipQuery->getQuery()->orders)) {
            $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($this->getRelationshipTitleAttribute()));
        }

        return $relationshipQuery;
    }
}
