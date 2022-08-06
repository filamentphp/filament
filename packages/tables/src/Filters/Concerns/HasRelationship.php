<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait HasRelationship
{
    protected ?Closure $modifyRelationshipQueryUsing = null;

    public function relationship(string $relationshipName, string $titleColumnName = null, Closure $callback = null): static
    {
        $this->column("{$relationshipName}.{$titleColumnName}");

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

        $relationshipQuery = $relationship->getRelated()->query()->orderBy($titleColumnName);

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        return $relationshipQuery
            ->pluck($titleColumnName, $this->getRelationshipKey())
            ->toArray();
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getColumn())->contains('.');
    }

    protected function getRelationship(): Relation | Builder
    {
        $model = app($this->getTable()->getModel());

        return $model->{$this->getRelationshipName()}();
    }

    protected function getRelationshipName(): string
    {
        return (string) Str::of($this->getColumn())->beforeLast('.');
    }

    protected function getRelationshipTitleColumnName(): string
    {
        return (string) Str::of($this->getColumn())->afterLast('.');
    }
}
