<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class RelationshipOrderer
{
    public function buildSubquery(EloquentBuilder $query, string $relationshipName, string $column): Builder
    {
        $relationshipChain = $this->buildRelationshipChain($query->getModel(), $relationshipName);
        $targetModel = $this->getTargetModel($relationshipChain);

        $subquery = $this->initializeSubquery($targetModel, $column);
        $this->applyRelationshipConstraints($subquery, $relationshipChain, $query->getModel());

        return $subquery->limit(1)->toBase();
    }

    protected function buildRelationshipChain(Model $baseModel, string $relationshipPath): array
    {
        $relationshipSegments = explode('.', $relationshipPath);
        $currentModel = $baseModel;
        $chain = [];

        foreach ($relationshipSegments as $relationshipSegment) {
            $relationship = $currentModel->{$relationshipSegment}();

            $this->validateRelationshipType($relationship);

            $chain[] = $relationship;

            $currentModel = $relationship->getRelated();
        }

        return $chain;
    }

    protected function validateRelationshipType(Relation $relationship): void
    {
        if ($relationship instanceof BelongsTo || $relationship instanceof HasOne || $relationship instanceof MorphOne) {
            return;
        }

        throw new InvalidArgumentException(
            'Nested sorting only supports [BelongsTo], [HasOne], and [MorphOne] relationships, [' . $relationship::class . '] found.'
        );
    }

    protected function getTargetModel(array $relationshipChain): Model
    {
        $lastRelationship = end($relationshipChain);

        return $lastRelationship->getRelated();
    }

    protected function initializeSubquery(Model $targetModel, string $column): EloquentBuilder
    {
        return $targetModel::query()->select($targetModel->qualifyColumn($column));
    }

    protected function applyRelationshipConstraints(
        EloquentBuilder $subquery,
        array $relationshipChain,
        Model $baseModel
    ): void {
        $chainLength = count($relationshipChain);

        for ($i = $chainLength - 1; $i >= 0; $i--) {
            $isFirstRelationship = $i === 0;

            if ($isFirstRelationship) {
                $this->applyFirstRelationshipConstraint($subquery, $relationshipChain[$i], $baseModel);
            } else {
                $this->applyIntermediateRelationshipJoin($subquery, $relationshipChain[$i], $relationshipChain[$i - 1]);
            }
        }
    }

    protected function applyFirstRelationshipConstraint(
        EloquentBuilder $subquery,
        BelongsTo | HasOne | MorphOne $relationship,
        Model $baseModel
    ): void {
        $baseTable = $baseModel->getTable();

        if ($relationship instanceof BelongsTo) {
            $this->applyBelongsToConstraint($subquery, $relationship, $baseTable);
        } elseif ($relationship instanceof MorphOne) {
            $this->applyMorphOneConstraint($subquery, $relationship, $baseModel);
        } elseif ($relationship instanceof HasOne) {
            $this->applyHasOneConstraint($subquery, $relationship, $baseModel);
        }
    }

    protected function applyBelongsToConstraint(
        EloquentBuilder $subquery,
        BelongsTo $relationship,
        string $baseTable
    ): void {
        $subquery->whereColumn(
            $relationship->getQualifiedOwnerKeyName(),
            $relationship->getQualifiedForeignKeyName(),
        );
    }

    protected function applyHasOneConstraint(
        EloquentBuilder $subquery,
        HasOne $relationship,
        Model $baseModel
    ): void {
        $subquery->whereColumn(
            $relationship->getQualifiedForeignKeyName(),
            $baseModel->qualifyColumn($relationship->getLocalKeyName()),
        );
    }

    protected function applyMorphOneConstraint(
        EloquentBuilder $subquery,
        MorphOne $relationship,
        Model $baseModel
    ): void {
        $subquery->whereColumn(
            $relationship->getQualifiedForeignKeyName(),
            $baseModel->qualifyColumn($relationship->getLocalKeyName()),
        )->where(
            $relationship->getQualifiedMorphType(),
            $relationship->getMorphClass()
        );
    }

    protected function applyIntermediateRelationshipJoin(
        EloquentBuilder $subquery,
        BelongsTo | HasOne | MorphOne $currentRelationship,
        BelongsTo | HasOne | MorphOne $previousRelationship
    ): void {
        $previousTable = $previousRelationship->getRelated()->getTable();

        if ($currentRelationship instanceof BelongsTo) {
            $this->joinBelongsTo($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof MorphOne) {
            $this->joinMorphOne($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof HasOne) {
            $this->joinHasOne($subquery, $currentRelationship, $previousTable);
        }
    }

    protected function joinBelongsTo(
        EloquentBuilder $subquery,
        BelongsTo $relationship,
        string $previousTable
    ): void {
        $subquery->join(
            $previousTable,
            $relationship->getQualifiedOwnerKeyName(),
            '=',
            $relationship->getQualifiedForeignKeyName(),
        );
    }

    protected function joinHasOne(
        EloquentBuilder $subquery,
        HasOne $relationship,
        string $previousTable
    ): void {
        $subquery->join(
            $previousTable,
            $relationship->getQualifiedForeignKeyName(),
            '=',
            $relationship->getQualifiedParentKeyName(),
        );
    }

    protected function joinMorphOne(
        EloquentBuilder $subquery,
        MorphOne $relationship,
        string $previousTable
    ): void {
        $subquery->join(
            $previousTable,
            $relationship->getQualifiedForeignKeyName(),
            '=',
            $relationship->getQualifiedParentKeyName(),
        )->where(
            $relationship->getQualifiedMorphType(),
            $relationship->getMorphClass(),
        );
    }
}
