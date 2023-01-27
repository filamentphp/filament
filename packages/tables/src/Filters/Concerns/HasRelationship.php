<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasRelationship
{
    protected ?Closure $modifyRelationshipQueryUsing = null;

    public function relationship(string $relationshipName, string $titleAttribute = null, Closure $callback = null): static
    {
        $this->attribute("{$relationshipName}.{$titleAttribute}");

        $this->modifyRelationshipQueryUsing = $callback;

        return $this;
    }

    public function getRelationshipKey(): string
    {
        return $this->getRelationship()->getRelated()->getQualifiedKeyName();
    }

    /**
     * @return array<scalar, string>
     */
    protected function getRelationshipOptions(): array
    {
        $relationship = $this->getRelationship();

        $titleAttribute = $this->getRelationshipTitleAttribute();

        $relationshipQuery = $relationship->getRelated()->query();

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if (empty($relationshipQuery->getQuery()->orders)) {
            $relationshipQuery->orderBy($titleAttribute);
        }

        return $relationshipQuery
            ->pluck($titleAttribute, $this->getRelationshipKey())
            ->toArray();
    }

    public function queriesRelationships(): bool
    {
        return str($this->getAttribute())->contains('.');
    }

    protected function getRelationship(): Relation | Builder
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

    protected function getRelationshipName(): string
    {
        return (string) str($this->getAttribute())->beforeLast('.');
    }

    protected function getRelationshipTitleAttribute(): string
    {
        return (string) str($this->getAttribute())->afterLast('.');
    }
}
