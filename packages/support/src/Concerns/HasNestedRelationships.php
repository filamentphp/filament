<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Heavily commented mostly for code review purposes, comments can be mostly stripped if PR is merged.
 *
 * This trait provides all the logic needed to handle distant relationship 'chains' (dotted names) in columns, either
 * from the column names themselves, or as things like Ordering or Grouping ID's / attributes.
 *
 * I wound up putting this trait in the ./support package, rather than folding it all in to the existing table columns
 * HasRelationships trait, because (while it replaces most stuff in that trait) it isn't just a column trait, it needs
 * to be used in things like Groups as well. There is no doubt a better place to put it.
 *
 * In this PR, the consumers of this trait (where I have changed code to use it) are:
 *
 * ========================
 * = HasState:
 * ========================
 *
 * ./packages/tables/src/Columns/Concerns/HasState.php
 *
 * In getStateFromRecord, after the simple Arr:get() check (which will find any non-dotted or simple belongsTo dotted
 * chains), if no value is found, it will then try $this->collectNestedAttributes() from this trait, which returns
 * a collected (merged) array of attributes from any arbitrary length dotted relationship chain, of any arbitrary mix of
 * relationship types (one and many).
 *
 * Additionally, this PR adds a CanFormatArrayState trait, with ul(), ol() and grid() methods, for formatting
 * arrays of data returned by collectNestedAttributes().  Should probably be merged into the CanFormatState trait, I
 * just kept it separate while developing.
 *
 * So, a column like ...
 *
 * TextColumn('comments.author.name')
 *    ->ul(),
 *
 * ... on a Post table, where Post HasMany 'comments', and 'comments' belongsTo 'author', would render all author's
 * names who have commented on a post as a <ul>...</ul>
 *
 * =============================
 * = InteractsWithTableQuery:
 * =============================
 *
 * ./packages/tables/src/Columns/Concerns/InteractsWithTableQuery.php
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
 * because the relationships at the start of the chain are not included in the query.  Hence this method,
 * which walks down the chain recursively nesting the existence queries.
 *
 * =============
 * = Grouping
 * =============
 *
 * ./packages/tables/src/Grouping/Group.php
 *
 * This PR modifies Group.php to support grouping on an arbitrary length chain of one-to-one relationships.
 *
 * The orderQuery method in the Group class uses $this->getNestedRelationshipExistenceQueries() from this class,
 * in the same way as InteractsWithTableQuery.
 *
 * The getGroupTitleFromRecord method in the Group class returns $this->getNestedAttribute() from this class, which
 * returns the attribute at the end of a one-to-one chain.
 *
 * The scopeQuery method in the Group class returns $this->getNestedWhere() from this class, which nests arbitrary
 * length one-to-one chains (included simple non-nested attributes), wrapping them in nested whereHas() clauses.
 *
 * ===============
 * = HasColumns
 * ===============
 *
 * ./packages/tables/src/Concerns/HasColumns.php
 *
 * The setColumnValue() method in HasColumns uses $column->getNestedRecord() from this trait, when updating
 * records from Editable columns.  This allows an Editable column to set values on distant relationships, as arbitrary
 * length one-to-one chains.
 */
trait HasNestedRelationships
{
    /**
     * Explode the (maybe) dotted name into an array, leaving attribute in as the last array entry.  So
     * 'post.author.name' becomes ['post', 'author', 'name'].  I experimented with using a Collection, but that
     * doesn't play well with recursion and shifting off a stack.
     *
     * @param  string  $name
     * @return array
     */
    public function getRelationshipStack(string $name): array
    {
        return explode('.', $name);
    }

    /**
     * Shifts the next relationship name off a stack and return the relationship method on the record for that
     * named relationship.  The last entry in a stack is always the target attribute, so if only one left in the stack,
     * it returns null.
     *
     * @param  array  $relationships
     * @param  Model  $record
     * @return Relation|null
     */
    public function shiftNextRelationship(array &$relationships, Model $record, ?string &$relationshipName = null): ?Relation
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
     * @param  array|string|null  $relationships
     * @param  Model|null  $record
     * @param  array|null  $results
     * @return array
     */
    public function collectNestedAttributes(array|string|null $relationships = null, ?Model $record = null, ?array &$results = null): array
    {
        // perform "first time" checks, see if we need to provide default values
        if (! isset($relationships) || is_string($relationships)) {
            // Allow calling with either no args, or specifying the $relationships as a dotted string, with or without
            // a record.  Record will default to $this->getRecord(), relationships will default to exploding $this->getName()
            $results = [];
            $record ??= $this->getRecord();
            $name = $relationships ?? $this->getName();

            if (! $this->queriesRelationships($record)) {
                // if no relationships, just an attribute, it wasn't really nested, just return it
                // via Laravel's getAttribute(), wrapped in an array

                return (array) $record->getAttribute($name);
            } else {
                // if we have actual relationships, get the relationship stack and start recursing, using either
                // the provided dotted name in $relationships, or defaulting to getName(), to build the stack

                return $this->collectNestedAttributes($this->getRelationshipStack($name), $record, $results);
            }
        } else {
            // at this point we've taken care of any first time checks and are somewhere in our recursion stack, so
            // shift the next relationship off the stack ...
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                // if it was the last relationship in the stack, get the records from it, push the attribute from
                // each one via Laravel's getAttribute() into an array, and return the array
                $attributes = [];

                foreach ($relationship->get() as $relatedRecord) {
                    $attributes[] = $relatedRecord->getAttribute($this->getStackAttribute($relationships));
                }

                return $attributes;
            } else {
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
     * @param  array|string|null  $relationships
     * @param  Model|null  $record
     * @return Model|null
     */
    public function getNestedRecord(array|string|null &$relationships = null, ?Model $record = null): ?Model
    {
        // first time checks, see if we need to set defaults
        if (! isset($relationships) || is_string($relationships)) {
            // Allow calling with either no args, or specifying the $relationships as a dotted string, with or without
            // a record.  Record will default to $this->getRecord(), relationships will default to exploding $this->getName()
            $record ??= $this->getRecord();
            $name = $relationships ?? $this->getName();

            // if it's not really nested, just an attribute, return null
            if (! $this->queriesRelationships($record, $name)) {
                return null;
            } else {
                // it's a ralationship, so build the stack and start recursing
                $relationships = $this->getRelationshipStack($name);

                return $this->getNestedRecord($relationships, $record);
            }
        } else {
            // first time checks are done, so we're into the recursion.
            // shift the next relationship off the stack ...
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                // if this was the last relationship, return the record
                return $relationship->getResults();
            } else {
                // if we haven't reached the end of the relationship chain, just recurse, passing the results of
                // this relationship
                return $this->getNestedRecord($relationships, $relationship->getResults());
            }
        }
    }

    /**
     * Non-destructively return the target attribute from a relationship stack, which is always the last array item.
     *
     * @param  array  $relationships
     * @return string
     */
    public function getStackAttribute(array $relationships): string
    {
        return $relationships[array_key_last($relationships)];
    }

    /**
     * Returns true if there are no more relationships in the stack, e.g. if the stack count === 1 (as the target
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
     * Returns the last relationship in the stack (before the attribute), null if no relationships (only attribute)
     *
     * (really only here to provide backward compat for InteractsWithTableQuery's getRelationship())
     *
     * @param  Model  $record
     * @param  array|string|null  $relationships
     * @return Relation|null
     */
    public function getLastRelationship(Model $record, array|string|null $relationships = null): ?Relation
    {
        if (! isset($relationships) || is_string($relationships)) {
            $relationships = $this->getRelationshipStack($relationships ?? $this->getName());
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
     * A clone of getLastRelationship() above, but returns the Model associated with the last relationship in a chain,
     * rather than the relationship itself.
     *
     * @param  Model  $record
     * @param  array|string|null  $relationships
     * @return Relation|null
     */
    public function getLastRelationshipRecord(Model $record, array|string|null $relationships = null): ?Model
    {
        if (! isset($relationships) || is_string($relationships)) {
            $relationships = $this->getRelationshipStack($relationships ?? $this->getName());
        }

        if ($this->isNoMoreRelationships($relationships)) {
            return null;
        } else {
            $relationship = $this->shiftNextRelationship($relationships, $record);

            if ($this->isNoMoreRelationships($relationships)) {
                return $record;
            } else {
                $record = $relationship->getRelated();

                return $this->getLastRelationship($record, $relationships);
            }
        }
    }

    /**
     * This probably belongs somewhere else, wasn't sure where to put it.  Used from anywhere that builds am orderBy()
     * query potentially from a nested relationship, like 'post.author.name' on a Comments table.  Returns a nested
     * set of relationshipExistenceQueries, with each successive one being in the 'column' arg of the previous.
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
                            ),
                        ],
                    )
                    ->applyScopes()
                    ->getQuery();
            }
        }
    }

    /**
     * Attempts to build an inverse relationship chain, using the assumption of standard Laravel naming conventions
     * for classes and relationship, from the provided forward chain and model from the parent table.  The inverse
     * chain will end with the primary key field of the parent table.
     *
     * So 'post.author.name' on a Comments table (so $record is a Comment model) might yield 'author.posts.comments.id'.
     * For each relationship in the forward chain, it first tries a pluralized, camel cased  version of that
     * relationships parent class, and if that doesn't exist, a singular camel case name.  If at any step neither
     * class exists on the model, it abandons ship and returns null.
     *
     * Based on code originally in Summarizer.php, which only handled a single level,  which now calls this method
     * instead, to handle any level of nesting.
     *
     * @param  string  $name
     * @param  Model  $record
     * @param  array|null  $relationships
     * @param  array|null  $reverse
     * @return array|null
     */
    public function getInverseRelationships(string $name, Model &$record, ?array $relationships = null, ?array $reverse = null, ?string $previousRelationshipName = null): ?array
    {
        if (! isset($relationships)) {
            $relationships = is_array($name) ? $name : $this->getRelationshipStack($name);
            $reverse = [$record->getKeyName()];
            $reversed = $this->getInverseRelationships($name, $record, $relationships, $reverse);

            if (! $reversed) {
                return null;
            }

            return array_reverse($reversed);
        } else {
            if ($this->isNoMoreRelationships($relationships)) {
                return $reverse;
            } else {
                $relationshipName = '';
                $relationship = $this->shiftNextRelationship($relationships, $record, $relationshipName);
                $record = $relationship->getRelated();

                $inverseRelationship = (string) str(class_basename($relationship->getParent()::class))
                    ->plural()
                    ->camel();

                if (! method_exists($record, $inverseRelationship)) {
                    $inverseRelationship = (string) str(class_basename($relationship->getParent()::class))
                        ->singular()
                        ->camel();

                    if (! method_exists($record, $inverseRelationship)) {
                        // Hail Mary, if it's a HasMany relationship, good chance the inverse is the same name
                        // as the previous relationship on the forward chain, like dailyLog.pilots.personnel.name
                        // would have a personnel.pilots...
                        $inverseRelationship = $previousRelationshipName;

                        if (! method_exists($record, $inverseRelationship)) {
                            // ... or if it's a BelongsToMany with a pivot, like dailyLog.pilots.name (though a
                            // personnel<->dailyLog pivot), then the reverse would be the same as the current forward
                            // name.
                            $inverseRelationship = $relationshipName;

                            if (! method_exists($record, $inverseRelationship)) {
                                // hey ho, oh well, give up and go home
                                return null;
                            }
                        }
                    }
                }

                $reverse[] = $inverseRelationship;

                return $this->getInverseRelationships($name, $record, $relationships, $reverse, $relationshipName);
            }
        }
    }

    /**
     * Create a nested whereHas clause, for any arbitrary depth of dotted relation.  See doc for getNestedWhere for
     * the theory.  Main difference is this expects a Closure for applying the whereHas() at the end of the chain.
     *
     * At time of writing, only called from Summarizer.php, as part of building the inverse relationship query for
     * collecting the target table ID's for group summaries that use distant relationships..
     *
     * @param  array|string  $name
     * @param  Builder  $query
     * @param  Model  $record
     * @param  Closure  $has
     * @param  array|null  $relationships
     * @return Builder
     */
    public function getNestedWhereHas(array|string $name, Builder $query, Model $record, Closure $has, ?array $relationships = null)
    {
        if (is_null($relationships)) {
            $relationships = is_array($name) ? $name : $this->getRelationshipStack($name);

            return $this->getNestedWhereHas($name, $query, $record, $has, $relationships);
        } else {
            $relationshipName = '';
            $relationship = $this->shiftNextRelationship($relationships, $record, $relationshipName);
            $record = $relationship->getRelated();

            if ($this->isNoMoreRelationships($relationships)) {
                return $query->whereHas(
                    $relationshipName,
                    fn (Builder $nestedQuery) => $this->evaluate($has, ['relatedQuery' => $nestedQuery])
                );
            } else {
                return $query->whereHas(
                    $relationshipName,
                    fn (Builder $nestedQuery) => $this->getNestedWhereHas($name, $nestedQuery, $record, $has, $relationships)
                );
            }
        }
    }

    /**
     * Builds a simple '=' where clause for an attribute, but nests inside successive whereHas() clauses if it's a
     * relationship chain.  So calling this on just an attribute would yield a simple where() clause ...
     *
     * $this->getNestedQuery($query, 'name', $record)
     *
     * ... yields ...
     *
     * $query->where('name', '=', $record->getAttribute('name'))
     *
     * ... but calling it on a dotted chain ...
     *
     * $this->getNestedQuery($query, 'post.author.name', $record)
     *
     * ... yields ...
     *
     * $query->whereHas(
     *     'post',
     *     fn (Builder $query) => $query->whereHas(
     *         'author',
     *          fn (Builder $query) => $query->where('name', '=', $record->getAttribute('name'))
     *     )
     *  )
     *
     * @param  Builder  $query
     * @param  string  $column
     * @param  Model  $record
     * @param  array|null  $relationships
     * @return Builder
     */
    public function getNestedWhere(Builder $query, string $column, Model $record, ?array $relationships = null)
    {
        if (! isset($relationships)) {
            $relationships = $this->getRelationshipStack($column);

            if ($this->isNoMoreRelationships($relationships)) {
                return $query->where(
                    $this->getStackAttribute($relationships),
                    '=',
                    $record->getAttribute($this->getStackAttribute($relationships))
                );
            } else {
                return $this->getNestedWhere($query, $column, $record, $relationships);
            }
        } else {
            $relationship = $this->shiftNextRelationship($relationships, $record);
            $record = $relationship->getResults();

            if ($this->isNoMoreRelationships($relationships)) {
                return $query->whereHas(
                    $relationship->getRelationName(),
                    fn (Builder $relatedQuery) => $relatedQuery->where(
                        $this->getStackAttribute($relationships),
                        '=',
                        $record->getAttribute($this->getStackAttribute($relationships))
                    )
                );
            } else {
                return $query->whereHas(
                    $relationship->getRelationName(),
                    fn (Builder $nestedQuery) => $this->getNestedWhere($nestedQuery, $column, $record, $relationships)
                );
            }
        }
    }

    /**
     * Legacy from InteractsWithTableQuery, changed to use new internal method
     *
     * @param  Model  $model
     * @param  ?string  $name
     * @return bool
     */
    public function queriesRelationships(Model $model, ?string $name = null): bool
    {
        return $this->getLastRelationship($model, $name) !== null;
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
