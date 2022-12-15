<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Tables\Concerns\HasNestedRelationships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasRelationship
{
    use HasNestedRelationships;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    public function relationship(string $relationshipName, string $titleAttribute = null, Closure $callback = null): static
    {
        $this->attribute("{$relationshipName}.{$titleAttribute}");

        $this->modifyRelationshipQueryUsing = $callback;

        return $this;
    }

    public function getRelationshipKey(): string
    {
        $relationship = $this->getRelationship();

        if ($relationship instanceof BelongsToMany) {
            $keyColumn = $relationship->getQualifiedRelatedKeyName();
        } elseif ($relationship instanceof HasOneThrough) {
            $keyColumn = $relationship->getQualifiedForeignKeyName();
        } elseif ($relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
            $keyColumn = $relationship->getRelated()->getQualifiedKeyName();
        } else {
            /** @var BelongsTo $relationship */
            $keyColumn = $relationship->getQualifiedOwnerKeyName();
        }

        return $keyColumn;
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
        $model = app($this->getTable()->getModel());

        return $model->{$this->getRelationshipName()}();
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
