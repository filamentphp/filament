<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Znck\Eloquent\Relations\BelongsToThrough;

trait HasRelationship
{
    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected bool | Closure $isPreloaded = false;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $relationshipTitleAttribute = null;

    public function relationship(string | Closure | null $name, string | Closure | null $titleAttribute, ?Closure $modifyQueryUsing = null): static
    {
        $this->relationship = $name;
        $this->relationshipTitleAttribute = $titleAttribute;
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
        return filled($this->getRelationshipName());
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

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
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

    public function getRelationshipKey(?Builder $query = null): ?string
    {
        $relationship = $this->getRelationship();

        if ($relationship instanceof BelongsToMany) {
            return $query?->getModel()->qualifyColumn($relationship->getRelatedKeyName()) ??
                $relationship->getQualifiedRelatedKeyName();
        }

        if ($relationship instanceof HasManyThrough) {
            return $query?->getModel()->qualifyColumn($relationship->getForeignKeyName()) ??
                $relationship->getQualifiedForeignKeyName();
        }

        if ($relationship instanceof BelongsToThrough) {
            return $relationship->getRelated()->getQualifiedKeyName();
        }

        if ($relationship instanceof BelongsTo) {
            return $query?->getModel()->qualifyColumn($relationship->getOwnerKeyName()) ??
                $relationship->getQualifiedOwnerKeyName();
        }

        return null;
    }
}
