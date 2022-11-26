<?php

namespace Filament\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Heavily commented mostly for code review purposes, comments can be mostly stripped if PR is merged.
 *
 * This trait provides all the logic needed to handle distant relationship 'chains' (dotted names) in columns, either
 * from the column names themselves, or as things like Ordering or Grouping ID's / attributes.
 *
 * In this PR, the consumers of this trait (where I have changed code to use this trait) are:
 *
 * ==== HasState:
 *
 * In getStateFromRecord, after the simple Arr:get(), if nothing found it will then try
 * $this->collectNestedAttributes() from this trait, which returns a collected array of attributes from an arbitrary
 * length dotted relationship chain, of any arbitrary mix of relationship types (one and many).
 *
 * Additionally, this PR adds a CanFormatArrayState trait, with ul(), ol() and grid() methods, for formatting
 * arrays of data returned by collectNestedAttributes().
 *
 * So, a column like ...
 *
 * TextColumn('comments.author.name')
 *    ->ul(),
 *
 * ... on a Post table, where Post HasMany 'comments', and 'comments' belongsTo 'author', would render all author's
 * names who have commented on a post as a <ul>...</ul>
 *
 * ==== InteractsWithTableQuery:
 *
 * In ApplySort, it uses $this->getNestedRelationshipExistenceQueries() from this trait in building the orderBy
 * query, like this ...
 *
 * $query->orderBy(
 *     $this->getNestedRelationshipExistenceQueries($query, $sortColumn, $direction),
 *     $direction
 * );
 *
 * ... where the original code had a binary When() approach that just checked the final relationship in a chain,
 * and used nestedRelationshipQuery if there was a relationship.  This doesn't work for any chains longer than one,
 * because the relationships at the start of the chain are not in included in the query.  Hence this method,
 * which walks down the chain recursively nesting the existence queries.
 *
 * ==== Grouping
 *
 * The orderQuery method in the Group class uses $this->getNestedRelationshipExistenceQueries() from this class,
 * in the same way as InteractsWithTableQuery.
 *
 * The getGroupTitleFromRecord method in the Group class returns $this->getnestedAttribute() from this class.
 *
 * The scopeQuery method in the Group class returns $this->getNestedWhere() from this class.
 *
 * ==== HasColumns
 *
 * The setColumnValue() method in HasColumns uses $column->getNestedRecord() from this trait, when updating
 * records from Editable columns.  This allows an Editable column to set values on distant relationships.
 *
 */
trait HasNestedRelationships
{

    /**
     * Explode the (maybe) dotted name into an array, leaving attribute in as the last array entry.  So
     * 'post.author.name' becomes ['post', 'author', 'name']
     *
     * @param string $name
     *
     * @return array
     */
    public function getRelationshipStack(string $name): array
    {
        return explode('.', $name);
    }

    /**
     * Shifts the next relationship name off a stack (array of dotted name components) and return the relationship
     * method on the record for that named relationship.  The last entry in a stack is always the target attribute,
     * so if only one left in the stack, it returns null.
     *
     * @param array $relationships
     * @param Model $record
     *
     * @return Relation|null
     */
    public function shiftNextRelationship(array &$relationships, Model $record): ?Relation
    {
        if (count($relationships) > 1) {
            $relationshipName = array_shift($relationships);

            if (!$record->isRelation($relationshipName)) {
                return null;
            }

            return $record->{$relationshipName}();
        }

        return null;
    }

    /**
     * Gets a singular attribute from a chain of one-to-one relationships, like 'comments.post.author.name',
     * will also work for non-nested, non-relational attributes like 'name'.  So on a Comments table ...
     *
     * $this->getNestedAttribute()
     *
     * ... or ...
     *
     * $this->getNestedAttribute('post.author.name', $comment)
     *
     * ... would return the Author relationship's 'name' attribute, from the Comment's Post relationship.
     *
     * Used for things like grouping titles, where it only makes sense to use one-to-one (BelongsTo) chains.
     *
     * @param string $name
     * @param Model  $record
     *
     * @return mixed
     */
    public function getNestedAttribute(string $name, Model $record): mixed
    {
        $relationships = $this->getRelationshipStack($name);

        if ($this->isNoMoreRelationships($relationships)) {
            return $record->getAttribute($name);
        }
        else {
            $record = $this->getNestedRecord($relationships, $record);

            return $record->getAttribute($this->getStackAttribute($relationships));
        }
    }

    /**
     * Collects an array of attributes from a chain of any length,  with any arbitrary mix of relations, including
     * "manies", like 'comments.author.name' on a Post table, with 'comments' being a HasMany.
     *
     * Called from getStateFromRecord() in HasState.
     *
     * Will work for all one-to-ones, but result will be an array with the single value (use getNestedAttribute if
     * you know you are dealing with just belongsTo chains). Will also work for non-nested, non-relational attribute
     * like 'name'.
     *
     * So ...
     *
     * $this->collectNestedAttributes()
     *
     * ... from a column GetState(), or ...
     *
     * $this->collectNestedAttributes('comments.author.name', $postRecord)
     *
     * ... from anywhere in column-land would return an array of the author names of all comments on a post.
     *
     * @param array|string|null $relationships
     * @param Model|null        $record
     * @param array|null        $results
     *
     * @return array
     */
    protected function collectNestedAttributes(array|string|null $relationships = null, ?Model $record = null, ?array &$results = null): array
    {
        // perform "first time" checks, see if we need to provide default values
        if (!isset($relationships) || is_string($relationships)) {
            // Allow calling with either no args, or specifying the $relationships as a dotted string, with or without
            // a record.  Record will default to $this->getRecord(), relationships will default to exploding $this->getName()
            $results = [];
            $record  ??= $this->getRecord();
            $name    = $relationships ?? $this->getName();

            if (!$this->queriesRelationships($record)) {
                // if no relationships, just an attribute, it wasn't really nested, just return it
                // via Laravel's getAttribute(), wrapped in an array

                return (array) $record->getAttribute($name);
            }
            else {
                // if we have actual relationships, get the relationship stack and start recursing, using either
                // the provided dotted name in $relationships, or defaulting to getName(), to build the stack

                return $this->collectNestedAttributes($this->getRelationshipStack($name), $record, $results);
            }
        }
        else {
            // at this point we've taken care of any first time checks and are somewhere in our recursion stack, so
            // shift the next relationship off the stackk ...
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                // if it was the last relationship in the stack, get the records from it, push the attribute from
                // each one via Laravel's getAttribute() into an array, and return the array
                $attributes = [];

                foreach ($relationship->get() as $relatedRecord) {
                    $attributes[] = $relatedRecord->getAttribute($this->getStackAttribute($relationships));
                }

                return $attributes;
            }
            else {
                // if we haven't reached the end of the relationship stack, iterate through the records of the current
                // relationship, recursing on each one, merging the returned results into our results array
                foreach ($relationship->get() as $relatedRecord) {
                    $results = array_merge(
                        $results,
                        $this->collectNestedAttributes($relationships, $relatedRecord, $results)
                    );
                }

                // and finally, return the merged results from the downstream recursions, which will either get
                // returned as the final result of the method, or merged into results upstream in the recursion stack
                return $results;
            }
        }
    }

    /**
     * Return the singular Model at the end of a chain of one-to-one relationships, but without 'resolving' the
     * actual attribute.  So for 'comments.post.author.name', will return the Author model from the Comment's
     * related Post.
     *
     * @param array|string|null $relationships
     * @param Model|null        $record
     *
     * @return Model|null
     */
    public function getNestedRecord(array|string|null &$relationships = null, ?Model $record = null): ?Model
    {
        // first time checks, see if we need to set defaults
        if (!isset($relationships) || is_string($relationships)) {
            // Allow calling with either no args, or specifying the $relationships as a dotted string, with or without
            // a record.  Record will default to $this->getRecord(), relationships will default to exploding $this->getName()
            $record ??= $this->getRecord();
            $name   = $relationships ?? $this->getName();

            // if it's not really nested, just an attribute, return null
            if (!$this->queriesRelationships($record, $name)) {
                return null;
            }
            else {
                // it's a ralationship, so build the stack and start recursing
                $relationships = $this->getRelationshipStack($name);

                return $this->getNestedRecord($relationships, $record);
            }
        }
        else {
            // first time checks are done, so we're into the recursion.
            // shift the next relationship off the stack ...
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                // if this was the last relationship, return the record
                return $relationship->getResults();
            }
            else {
                // if we haven't reached the end of the relationship chain, just recurse, passing the results of
                // this relationship
                return $this->getNestedRecord($relationships, $relationship->getResults());
            }
        }
    }

    /**
     * Non-destructively return the target attribute from a relationship stack, which is always the last array item.
     *
     * @param array $relationships
     *
     * @return string
     */
    public function getStackAttribute(array $relationships): string
    {
        return $relationships[array_key_last($relationships)];
    }

    /**
     * Returns false if there are no more relationships in the stack, e.g. if the stack count > 1 (as the target
     * attribute is always the last item in the stack)
     *
     * @param array $relationships
     *
     * @return bool
     */
    public function isNoMoreRelationships(array $relationships): bool
    {
        return count($relationships) === 1;
    }

    /**
     * Returns the last relationship in the stack (before the attribute), null if no relationships (only attribute)
     *
     * (really only here to provide backward compat for InteractsWithTableQuery's getRelationship())
     *
     * @param Model             $record
     * @param array|string|null $relationships
     *
     * @return Relation|null
     */
    public function getLastRelationship(Model $record, array|string|null $relationships = null): ?Relation
    {
        if (!isset($relationships) || is_string($relationships)) {
            $relationships = $this->getRelationshipStack($relationships ?? $this->getName());
        }

        if ($this->isNoMoreRelationships($relationships)) {
            return null;
        }
        else {
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                return $relationship;
            }
            else {
                $record = $relationship->getRelated();

                return $this->getLastRelationship($record, $relationships);
            }
        }
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
     * @param Builder     $query
     * @param             $name
     * @param string|null $direction
     * @param array|null  $relationships
     *
     * @return string|\Illuminate\Database\Query\Builder
     */
    protected function getNestedRelationshipExistenceQueries(Builder $query, $name, ?string $direction = 'asc', ?array $relationships = null): string|\Illuminate\Database\Query\Builder
    {
        if (!isset($relationships)) {
            $relationships = $this->getRelationshipStack($name);

            return $this->getNestedRelationshipExistenceQueries($query, $name, $direction, $relationships);
        }
        else {
            if ($this->isNoMoreRelationships($relationships)) {
                return $this->getStackAttribute($relationships);
            }
            else {
                $record       = $query->getModel();
                $relationship = $this->shiftNextRelationship($relationships, $record);
                $parentQuery  = $relationship->getRelated()::query();

                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
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
                            )
                        ],
                    )
                    ->applyScopes()
                    ->getQuery();
            }
        }
    }

    public function getNestedWhere(Builder $query, string $column, Model $record, ?array $relationships = null)
    {
        if (!isset($relationships)) {
            $relationships = $this->getRelationshipStack($column);

            if ($this->isNoMoreRelationships($relationships)) {
                return $query->where(
                    $this->getStackAttribute($relationships),
                    '=',
                    $record->getAttribute($this->getStackAttribute($relationships))
                );
            }
            else {
                return $this->getNestedWhere($query, $column, $record, $relationships);
            }
        }
        else {
            $relationship = $this->shiftNextRelationship($relationships, $record);
            $record       = $relationship->getResults();

            if ($this->isNoMoreRelationships($relationships)) {
                return $query->where(
                    $this->getStackAttribute($relationships),
                    '=',
                    $record->getAttribute($this->getStackAttribute($relationships))
                );
            }
            else {
                return $query->whereHas(
                    $relationship->getRelationName(),
                    fn(Builder $nestedQuery) => $this->getNestedWhere($nestedQuery, $column, $record, $relationships)
                );
            }
        }
    }

    /**
     * Legacy from InteractsWithTableQuery, changed to use new internal method
     *
     * @param Model   $model
     * @param ?String $name
     *
     * @return bool
     */
    public function queriesRelationships(Model $model, ?string $name = null): bool
    {
        return $this->getLastRelationship($model, $name) !== null;
    }

    /**
     * Legacy from InteractsWithTableQuery, returns string before last dot
     *
     * @param string|null $name
     *
     * @return string
     */
    public function getRelationshipName(?string $name = null): string
    {
        return (string) str($name ?? $this->getName())->beforeLast('.');
    }

    /**
     * Legacy from InteractsWithTableQuery, return string after last dot
     *
     * @param string|null $name
     *
     * @return string
     */
    public function getRelationshipAttribute(?string $name = null): string
    {
        return (string) str($name ?? $this->getName())->afterLast('.');
    }
}
