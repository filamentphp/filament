<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;
use Znck\Eloquent\Relations\BelongsToThrough;

class RelationshipOrderer
{
    public function buildSubquery(EloquentBuilder $query, string $relationshipName, string $column): Builder
    {
        $relationshipChain = $this->buildRelationshipChain($query->getModel(), $relationshipName);
        $lastRelationship = end($relationshipChain);
        $targetModel = $lastRelationship->getRelated();

        $subquery = $lastRelationship->getQuery()->select($targetModel->qualifyColumn($column));
        $this->applyRelationshipConstraints($subquery, $relationshipChain, $query->getModel());

        return $subquery->limit(1)->toBase();
    }

    /**
     * @return array<Relation>
     */
    protected function buildRelationshipChain(Model $baseModel, string $relationshipPath): array
    {
        $relationshipSegments = explode('.', $relationshipPath);
        $currentModel = $baseModel;
        $chain = [];

        foreach ($relationshipSegments as $relationshipSegment) {
            $relationship = Relation::noConstraints(fn () => $currentModel->{$relationshipSegment}());

            $this->validateRelationshipType($relationship);

            $chain[] = $relationship;

            $currentModel = $relationship->getRelated();
        }

        return $chain;
    }

    protected function validateRelationshipType(Relation $relationship): void
    {
        if ($relationship instanceof BelongsTo || $relationship instanceof HasOne || $relationship instanceof MorphOne || $relationship instanceof BelongsToThrough || $relationship instanceof HasOneThrough) {
            return;
        }

        throw new InvalidArgumentException(
            'Nested sorting only supports [BelongsTo], [HasOne], [MorphOne], [BelongsToThrough], and [HasOneThrough] relationships, [' . $relationship::class . '] found.'
        );
    }

    /**
     * @param  array<Relation>  $relationshipChain
     */
    protected function applyRelationshipConstraints(
        EloquentBuilder $subquery,
        array $relationshipChain,
        Model $baseModel
    ): void {
        $chainLength = count($relationshipChain);

        for ($i = $chainLength - 1; $i >= 0; $i--) {
            $isFirstRelationship = $i === 0;
            $isSubqueryBase = $i === ($chainLength - 1);

            if ($isFirstRelationship) {
                $this->applyFirstRelationshipConstraint($subquery, $relationshipChain[$i], $baseModel, $isSubqueryBase); /** @phpstan-ignore argument.type */
            } else {
                $this->applyIntermediateRelationshipJoin($subquery, $relationshipChain[$i], $relationshipChain[$i - 1], $isSubqueryBase); /** @phpstan-ignore argument.type, argument.type */
            }
        }
    }

    protected function applyFirstRelationshipConstraint(
        EloquentBuilder $subquery,
        BelongsTo | HasOne | MorphOne | BelongsToThrough | HasOneThrough $relationship,
        Model $baseModel,
        bool $isSubqueryBase,
    ): void {
        $baseTable = $baseModel->getTable();

        if ($relationship instanceof BelongsTo) {
            $this->applyBelongsToConstraint($subquery, $relationship, $baseTable);
        } elseif ($relationship instanceof MorphOne) {
            $this->applyMorphOneConstraint($subquery, $relationship, $baseModel);
        } elseif ($relationship instanceof HasOne) {
            $this->applyHasOneConstraint($subquery, $relationship, $baseModel);
        } elseif ($relationship instanceof BelongsToThrough) {
            $this->applyBelongsToThroughConstraint($subquery, $relationship, $baseModel, $isSubqueryBase);
        } elseif ($relationship instanceof HasOneThrough) {
            $this->applyHasOneThroughConstraint($subquery, $relationship, $isSubqueryBase);
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

    protected function applyBelongsToThroughConstraint(
        EloquentBuilder $subquery,
        BelongsToThrough $relationship,
        Model $baseModel,
        bool $isSubqueryBase,
    ): void {
        if (! $isSubqueryBase) {
            $this->joinBelongsToThroughParents($subquery, $relationship);
        }

        $subquery->whereColumn(
            $relationship->getQualifiedFirstLocalKeyName(),
            $baseModel->qualifyColumn($relationship->getFirstForeignKeyName()),
        );
    }

    protected function applyHasOneThroughConstraint(
        EloquentBuilder $subquery,
        HasOneThrough $relationship,
        bool $isSubqueryBase,
    ): void {
        if (! $isSubqueryBase) {
            $this->joinHasOneThroughParent($subquery, $relationship);
        }

        $subquery->whereColumn(
            $relationship->getQualifiedFirstKeyName(),
            $relationship->getQualifiedLocalKeyName(),
        );
    }

    protected function joinBelongsToThroughParents(
        EloquentBuilder $subquery,
        BelongsToThrough $relationship,
    ): void {
        $throughParents = $relationship->getThroughParents();

        foreach ($throughParents as $i => $throughParent) {
            $predecessor = ($i === 0)
                ? $relationship->getRelated()
                : $throughParents[$i - 1];

            $subquery->join(
                $throughParent->getTable(),
                $throughParent->qualifyColumn($relationship->getForeignKeyName($predecessor)),
                '=',
                $predecessor->qualifyColumn($relationship->getLocalKeyName($predecessor)),
            );
        }
    }

    protected function joinHasOneThroughParent(
        EloquentBuilder $subquery,
        HasOneThrough $relationship,
    ): void {
        $subquery->join(
            invade($relationship)->throughParent->getTable(), /** @phpstan-ignore property.protected */
            $relationship->getQualifiedParentKeyName(),
            '=',
            $relationship->getQualifiedFarKeyName(),
        );
    }

    protected function applyIntermediateRelationshipJoin(
        EloquentBuilder $subquery,
        BelongsTo | HasOne | MorphOne | BelongsToThrough | HasOneThrough $currentRelationship,
        BelongsTo | HasOne | MorphOne | BelongsToThrough | HasOneThrough $previousRelationship,
        bool $isSubqueryBase,
    ): void {
        $previousTable = $previousRelationship->getRelated()->getTable();

        if ($currentRelationship instanceof BelongsTo) {
            $this->joinBelongsTo($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof MorphOne) {
            $this->joinMorphOne($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof HasOne) {
            $this->joinHasOne($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof BelongsToThrough) {
            if (! $isSubqueryBase) {
                $this->joinBelongsToThroughParents($subquery, $currentRelationship);
            }

            $this->joinBelongsToThrough($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof HasOneThrough) {
            if (! $isSubqueryBase) {
                $this->joinHasOneThroughParent($subquery, $currentRelationship);
            }

            $this->joinHasOneThrough($subquery, $currentRelationship, $previousTable);
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

    protected function joinBelongsToThrough(
        EloquentBuilder $subquery,
        BelongsToThrough $relationship,
        string $previousTable
    ): void {
        $throughParents = $relationship->getThroughParents();
        $lastThroughParent = end($throughParents);

        $subquery->join(
            $previousTable,
            $lastThroughParent->qualifyColumn($relationship->getLocalKeyName($lastThroughParent)),
            '=',
            "{$previousTable}.{$relationship->getForeignKeyName($lastThroughParent)}",
        );
    }

    protected function joinHasOneThrough(
        EloquentBuilder $subquery,
        HasOneThrough $relationship,
        string $previousTable
    ): void {
        $subquery->join(
            $previousTable,
            $relationship->getQualifiedLocalKeyName(),
            '=',
            $relationship->getQualifiedFirstKeyName(),
        );
    }
}
