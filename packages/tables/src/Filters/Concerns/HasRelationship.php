<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait HasRelationship
{
    protected ?Closure $modifyRelationshipQueryUsing = null;

    public function relationship(string $relationshipName, string $titleColumnName = null, Closure $callback = null): static
    {
        $this->attribute("{$relationshipName}.{$titleColumnName}");

        $this->modifyRelationshipQueryUsing = $callback;

        return $this;
    }

    public function getRelationshipKey(): string
    {
        $relationship = $this->getRelationship();

        if ($relationship instanceof BelongsToMany) {
            $keyColumn = $relationship->getQualifiedRelatedKeyName();
        } elseif ($relationship instanceof HasManyThrough) {
            $keyColumn = $relationship->getQualifiedForeignKeyName();
        } elseif ($relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
            $keyColumn = $relationship->getRelated()->getQualifiedKeyName();
        } else {
            /** @var BelongsTo $relationship */
            $keyColumn = $relationship->getQualifiedOwnerKeyName();
        }

        return $keyColumn;
    }

    protected function getRelationshipOptions(): array
    {
        $relationship = $this->getRelationship();

        $titleColumnName = $this->getRelationshipTitleColumnName();

        $relationshipQuery = $relationship->getRelated()->query();

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if (empty($relationshipQuery->getQuery()->orders)) {
            $relationshipQuery->orderBy($titleColumnName);
        }

        return $relationshipQuery
            ->pluck($titleColumnName, $this->getRelationshipKey())
            ->toArray();
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getAttribute())->contains('.');
    }

    protected function getRelationship(): Relation | Builder
    {
        $model = app($this->getTable()->getModel());

        return $model->{$this->getRelationshipName()}();
    }

    protected function getRelationshipName(): string
    {
        return (string) Str::of($this->getAttribute())->beforeLast('.');
    }

    protected function getRelationshipTitleColumnName(): string
    {
        return (string) Str::of($this->getAttribute())->afterLast('.');
    }
}
