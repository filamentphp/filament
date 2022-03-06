<?php

namespace Filament\Tables\Filters\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait HasRelationship
{
    public function relationship(string $relationshipName, string $displayColumnName = null): static
    {
        $this->column("{$relationshipName}.{$displayColumnName}");

        return $this;
    }

    public function getRelationshipKey(): string
    {
        $relationship = $this->getRelationship();

        if ($relationship instanceof MorphToMany) {
            $keyColumn = $relationship->getParentKeyName();
        } else {
            /** @var BelongsTo $relationship */
            $keyColumn = $relationship->getOwnerKeyName();
        }

        return $keyColumn;
    }

    protected function getRelationshipOptions(): array
    {
        $relationship = $this->getRelationship();

        $displayColumnName = $this->getRelationshipDisplayColumnName();

        $relationshipQuery = $relationship->getRelated()->query()->orderBy($displayColumnName);

        return $relationshipQuery
            ->pluck($displayColumnName, $this->getRelationshipKey())
            ->toArray();
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getColumn())->contains('.');
    }

    protected function getRelationship(): Relation
    {
        $model = app($this->getTable()->getModel());

        return $model->{$this->getRelationshipName()}();
    }

    protected function getRelationshipName(): string
    {
        return (string) Str::of($this->getColumn())->beforeLast('.');
    }

    protected function getRelationshipDisplayColumnName(): string
    {
        return (string) Str::of($this->getColumn())->afterLast('.');
    }
}
