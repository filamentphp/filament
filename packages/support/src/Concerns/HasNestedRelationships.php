<?php

namespace Filament\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasNestedRelationships
{
    public function getRelationshipStack(string $name): array
    {
        return explode('.', $name);
    }

    /**
     * Gets a singular attribute from a chain of one-to-one relationships, like 'comments.post.author.name',
     * will also work for non-nested, non-relational attributes like 'name'.
     *
     * @param  string  $name
     * @param  Model  $record
     * @return mixed
     */
    public function getNestedAttribute(string $name, Model $record): mixed
    {
        $relationships = $this->getRelationshipStack($name);

        if ($this->isNoMoreRelationships($relationships)) {
            return $record->getAttribute($name);
        } else {
            $record = $this->getNestedRecord($relationships, $record);

            return $record->getAttribute($this->getStackAttribute($relationships));
        }
    }

    /**
     * Gets an array of attributes from a chain with any arbitrary mix of relations, including "manies",
     * like 'post.comments.author.name'.  Will work for all one-to-ones, but result will be an array with
     * the single value (use getNestedAttribute if you know you are dealing with just belongsTo chains).
     * Will also work for non-nested, non-relational attribute like 'name'.
     *
     * @param  array|null  $relationships
     * @param  Model|null  $record
     * @param  array|null  $results
     * @return array
     */
    protected function collectNestedAttributes(?array $relationships = null, ?Model $record = null, ?array &$results = null): array
    {
        if (! isset($relationships)) {
            $results = [];
            $record ??= $this->getRecord();

            if (! $this->queriesRelationships($record)) {
                return (array) $record->getAttribute($this->getName());
            } else {
                $relationships = $this->getRelationshipStack($this->getName());

                return $this->collectNestedAttributes($relationships, $record ?? $this->getRecord(), $results);
            }
        } else {
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                $attributes = [];

                foreach ($relationship->get() as $relatedRecord) {
                    $attributes[] = $relatedRecord->getAttribute($this->getStackAttribute($relationships));
                }

                return $attributes;
            } else {
                foreach ($relationship->get() as $relatedRecord) {
                    $results = array_merge(
                        $results,
                        $this->collectNestedAttributes($relationships, $relatedRecord, $results)
                    );
                }

                return $results;
            }
        }
    }

    /**
     * Return the singular Model at the end of a chain of one-to-one relationships, like 'comments.post.author.name',
     * will return the Author model from the Comment's related Post.
     *
     * @param  array|null  $relationships
     * @param  Model|null  $record
     * @return Model|null
     */
    public function getNestedRecord(?array &$relationships = null, ?Model $record = null): ?Model
    {
        if (! isset($relationships)) {
            if (! $this->queriesRelationships($record ?? $this->getRecord())) {
                return null;
            } else {
                $relationships = $this->getRelationshipStack($this->getName());

                return $this->getNestedRecord($relationships, $record ?? $this->getRecord());
            }
        } else {
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                return $relationship->getResults();
            } else {
                return $this->getNestedRecord($relationships, $relationship->getResults());
            }
        }
    }

    /**
     * Shifts the next reletionship name off a stack (array of dotted name components) and return the relationship
     * method on the record for that named relationship.  The last entry in a stack is always the target attribute,
     * so if only one left in the stack, it returns null.
     *
     * @param  array  $relationships
     * @param  Model  $record
     * @return Relation|null
     */
    public function shiftNextRelationship(array &$relationships, Model $record): ?Relation
    {
        if (count($relationships) > 1) {
            $relationshipName = array_shift($relationships);

            if (! $record->isRelation($relationshipName)) {
                return null;
            }

            return $record->{$relationshipName}();
        }

        return null;
    }

    /**
     * Return the target attribute from a relationship stack, which is always the last array item.
     *
     * @param  array  $relationships
     * @return string
     */
    public function getStackAttribute(array &$relationships): string
    {
        return $relationships[array_key_last($relationships)];
    }

    /**
     * Returns the last relationship in the stack (before the attribute), null if no relationships (only attribute)
     *
     * (really only here to provide backward compat for InteractsWithTableQuery's getRelationship())
     *
     * @param  array|null  $relationships
     * @return Relation|null
     */
    public function getLastRelationship(Model $record, ?array $relationships = null): ?Relation
    {
        if (! isset($relationships)) {
            $relationships = $this->getRelationshipStack($this->getName());
        }

        if ($this->isNoMoreRelationships($relationships)) {
            return null;
        } else {
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                return $relationship;
            } else {
                $record = $relationship->getRelated();

                return $this->getLastRelationship($record, $relationships);
            }
        }
    }

    /**
     * Returns whether there is any more relationships in the stack, e.g. if the stack count > 1 (as the target
     * attribute is always the last item in the stack)
     *
     * @param  array  $relationships
     * @return bool
     */
    public function isNoMoreRelationships(array $relationships): bool
    {
        return count($relationships) === 1;
    }

    /**
     * This probably belongs somewhere else, wasn't sure where to put it.  Used from anywhere that builds am orderBy()
     * query potentially from a nested relationship, like 'post.author.name' on a Comments table.  Returns a nested
     * set of relationshipExistanceQueries, with each successive one being in the 'column' arg of the previous.
     *
     * Called as ...
     *
     * $query->orderBy(
     *     $this->getNestedRelationshipExistenceQueries($query, 'post.author.name', 'asc'),
     *     'asc'
     * );
     *
     * Without doing this recursively, can't order on anything more than a single dot, so (say) 'post.title' works,
     * but 'post.author.name' doesn't, because the first relationship isn't included in the query.
     *
     * @param  Builder  $query
     * @param    $name
     * @param  string|null  $direction
     * @param  array|null  $relationships
     * @return string|\Illuminate\Database\Query\Builder
     */
    protected function getNestedRelationshipExistenceQueries(Builder $query, $name, ?string $direction = 'asc', ?array $relationships = null): string|\Illuminate\Database\Query\Builder
    {
        if (! isset($relationships)) {
            $relationships = $this->getRelationshipStack($name);

            return $this->getNestedRelationshipExistenceQueries($query, $name, $direction, $relationships);
        } else {
            if ($this->isNoMoreRelationships($relationships)) {
                return $this->getStackAttribute($relationships);
            } else {
                $record = $query->getModel();
                $relationship = $this->shiftNextRelationship($relationships, $record);
                $parentQuery = $relationship->getRelated()::query();

                return $relationship
                    ->getRelationExistenceQuery(
                        $parentQuery,
                        $query,
                        [
                            $relationship->getRelationName() => $this->getNestedRelationshipExistenceQueries(
                                $parentQuery,
                                $name,
                                $direction,
                                $relationships
                            ),
                        ],
                    )
                    ->applyScopes()
                    ->getQuery();
            }
        }
    }

    /**
     * Legacy from InteractsWithTableQuery, changed to use new internal method
     *
     * @param  Model  $model
     * @return bool
     */
    public function queriesRelationships(Model $model): bool
    {
        return $this->getLastRelationship($model) !== null;
    }

    /**
     * Legacy from InteractsWithTableQuery, returns string before last dot
     *
     * @param  string|null  $name
     * @return string
     */
    public function getRelationshipName(?string $name = null): string
    {
        return (string) str($name ?? $this->getName())->beforeLast('.');
    }

    /**
     * Legacy from InteractsWithTableQuery, return string after last dot
     *
     * @param  string|null  $name
     * @return string
     */
    public function getRelationshipAttribute(?string $name = null): string
    {
        return (string) str($name ?? $this->getName())->afterLast('.');
    }
}
