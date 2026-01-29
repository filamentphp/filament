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
        $targetModel = $this->getTargetModel($relationshipChain);

        $subquery = $this->initializeSubquery($targetModel, $column);
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
            $relationship = $currentModel->{$relationshipSegment}();

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
    protected function getTargetModel(array $relationshipChain): Model
    {
        $lastRelationship = end($relationshipChain);

        return $lastRelationship->getRelated();
    }

    protected function initializeSubquery(Model $targetModel, string $column): EloquentBuilder
    {
        return $targetModel::query()->select($targetModel->qualifyColumn($column));
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

            if ($isFirstRelationship) {
                $this->applyFirstRelationshipConstraint($subquery, $relationshipChain[$i], $baseModel); /** @phpstan-ignore argument.type */
            } else {
                $this->applyIntermediateRelationshipJoin($subquery, $relationshipChain[$i], $relationshipChain[$i - 1]); /** @phpstan-ignore argument.type, argument.type */
            }
        }
    }

    protected function applyFirstRelationshipConstraint(
        EloquentBuilder $subquery,
        BelongsTo | HasOne | MorphOne | BelongsToThrough | HasOneThrough $relationship,
        Model $baseModel
    ): void {
        $baseTable = $baseModel->getTable();

        if ($relationship instanceof BelongsTo) {
            $this->applyBelongsToConstraint($subquery, $relationship, $baseTable);
        } elseif ($relationship instanceof MorphOne) {
            $this->applyMorphOneConstraint($subquery, $relationship, $baseModel);
        } elseif ($relationship instanceof HasOne) {
            $this->applyHasOneConstraint($subquery, $relationship, $baseModel);
        } elseif ($relationship instanceof BelongsToThrough) {
            $this->applyBelongsToThroughConstraint($subquery, $relationship, $baseModel);
        } elseif ($relationship instanceof HasOneThrough) {
            $this->applyHasOneThroughConstraint($subquery, $relationship);
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
    ): void {
        $throughParents = $relationship->getThroughParents();

        foreach ($throughParents as $i => $throughParent) {
            $isFirstThroughParent = $i === 0;

            if ($isFirstThroughParent) {
                $predecessor = $relationship->getRelated();
                $first = $throughParent->qualifyColumn($relationship->getForeignKeyName($predecessor));
                $second = $predecessor->qualifyColumn($relationship->getLocalKeyName($predecessor));

                $subquery->join($throughParent->getTable(), $first, '=', $second);
            } else {
                $predecessor = $throughParents[$i - 1];
                $first = $throughParent->qualifyColumn($relationship->getForeignKeyName($predecessor));
                $second = $predecessor->qualifyColumn($relationship->getLocalKeyName($predecessor));

                $subquery->join($throughParent->getTable(), $first, '=', $second);
            }
        }

        $subquery->whereColumn(
            $relationship->getQualifiedFirstLocalKeyName(),
            $baseModel->qualifyColumn($relationship->getFirstForeignKeyName()),
        );
    }

    protected function applyHasOneThroughConstraint(EloquentBuilder $subquery, HasOneThrough $relationship): void
    {
        /** @var Model $throughParent */
        $throughParent = invade($relationship)->throughParent; /** @phpstan-ignore property.protected */
        $subquery->join(
            $throughParent->getTable(),
            $relationship->getQualifiedFirstKeyName(),
            '=',
            $relationship->getQualifiedLocalKeyName()
        );

        $subquery->whereColumn(
            $relationship->getQualifiedForeignKeyName(),
            $relationship->getQualifiedParentKeyName()
        );
    }

    protected function applyIntermediateRelationshipJoin(
        EloquentBuilder $subquery,
        BelongsTo | HasOne | MorphOne | BelongsToThrough | HasOneThrough $currentRelationship,
        BelongsTo | HasOne | MorphOne | BelongsToThrough | HasOneThrough $previousRelationship
    ): void {
        $previousTable = $previousRelationship->getRelated()->getTable();

        if ($currentRelationship instanceof BelongsTo) {
            $this->joinBelongsTo($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof MorphOne) {
            $this->joinMorphOne($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof HasOne) {
            $this->joinHasOne($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof BelongsToThrough) {
            $this->joinBelongsToThrough($subquery, $currentRelationship, $previousTable);
        } elseif ($currentRelationship instanceof HasOneThrough) {
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
        $targetModel = $relationship->getRelated();

        // Join through parents from target to previousTable
        // For User->Company via Team: join Team to Company, then User to Team
        foreach ($throughParents as $i => $throughParent) {
            $isFirstThroughParent = $i === 0;

            if ($isFirstThroughParent) {
                // Join first through parent to the target model
                $subquery->join(
                    $throughParent->getTable(),
                    $targetModel->qualifyColumn($relationship->getLocalKeyName($targetModel)),
                    '=',
                    $throughParent->qualifyColumn($relationship->getForeignKeyName($targetModel)),
                );
            } else {
                // Join subsequent through parents
                $predecessor = $throughParents[$i - 1];
                $subquery->join(
                    $throughParent->getTable(),
                    $predecessor->qualifyColumn($relationship->getLocalKeyName($predecessor)),
                    '=',
                    $throughParent->qualifyColumn($relationship->getForeignKeyName($predecessor)),
                );
            }
        }

        // Finally, join the previous table to the last through parent
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
        /** @var Model $throughParent */
        $throughParent = invade($relationship)->throughParent; /** @phpstan-ignore property.protected */
        $subquery->join(
            $throughParent->getTable(),
            $relationship->getQualifiedParentKeyName(),
            '=',
            $relationship->getQualifiedFarKeyName()
        );

        $subquery->join(
            $previousTable,
            $relationship->getQualifiedLocalKeyName(),
            '=',
            $relationship->getQualifiedFirstKeyName(),
        );
    }
}
