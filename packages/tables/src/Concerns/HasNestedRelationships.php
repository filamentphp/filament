<?php

namespace Filament\Tables\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Heavily commented mostly for code review purposes, comments can be mostly stripped if PR is merged.
 *
 * This trait provides all the logic needed to handle distant relationship 'chains' (dotted names) in columns, either
 * from the column names themselves, or as things like Ordering or Grouping ID's / attributes.  In general, methods
 * in this trait will work with non-dotted, non nested attributes as well, so can be called without needing to
 * check if the attribute queries relationships.
 *
 * I'm expecting Dan to rewrite most of this code, and it's intended as a proof of concept for a "stack" based
 * approach to relationship handling.  As such, it's a "black box" - however the internals are written, the API
 * it provides would probably remain intact, as whatever way you slice it, the places detailed below where I've
 * changed code elsewhere in Filament to use these methods would need changing regardless, to call similar methods.
 *
 * I wound up putting this trait in its own concern, rather than folding it all in to the existing table columns
 * HasRelationships trait, because (while it replaces most stuff in that trait) it isn't just a column trait, it needs
 * to be used in things like Groups as well.
 *
 * In this PR, the consumers of this trait (where I have changed code to use it) are as follows.  I've notated
 * all these changes with ...
 *
 * // $$$ hugh
 *
 * ... so searching for that will find you everywhere I've made a change
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
 * In ApplySort, it uses $this->getNestedRelationExistenceQuery() from this trait in building the orderBy
 * query, like this ...
 *
 * $query->orderBy(
 *     $this->getNestedRelationExistenceQuery($query, $sortColumn),
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
 * The orderQuery method in the Group class uses $this->getNestedOrderBy() from this class, which is just a
 * convenience method for returning the getNestedRelationExistenceQuery() order by explained above.
 *
 * The getGroupTitleFromRecord method in the Group class returns $this->getNestedAttribute() from this class, which
 * returns the attribute at the end of a one-to-one chain.
 *
 * The scopeQuery method in the Group class returns $this->getNestedQuery() from this class, which nests arbitrary
 * length one-to-one chains (included simple non-nested attributes), wrapping them in nested whereHas() clauses,
 * with a closure for the actual where clause.
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
 *
 * ===============
 * = Summarizer
 * ===============
 *
 * ./packages/tables/src/Columns/Summarizers/Summarizer.php
 *
 * In getState(), the handling of relationships has been updated to use two methods from this trait:
 *
 * It uses $column->getInverseRelationships() to build the inverse relationship path, which uses the original
 * logic, with some added guesswork (see comments in that method).  If people have used standard Laravel naming
 * conventions, and have all their model relationships in place, the new code should work for most combinations,
 * including BelongsToMany and HasMany.  If not, they'll have to use the inverseRelationshipName() method to
 * provide their reverse path.
 *
 * It uses $column->getNestedQuery() to apply the necessary where clause for selecting the parent table ID's,
 * depending on the kind of summary being built.  Again, it's the original code, but applied through a nested
 * method with a Closure, so it can handle distant relationships.
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
     * Gets a singular attribute from a chain of one-to-one relationships, like 'post.author.name' on a Comments table,
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
     * you know you are dealing with one-ro-one chains). Will also work for non-nested, non-relational attribute
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
    public function collectNestedAttributes(array | string | null $relationships = null, ?Model $record = null, ?array &$results = null): array
    {
        // perform "first time" checks, see if we need to provide default values
        if (! isset($relationships) || is_string($relationships)) {
            // Allow calling with either no args, or specifying the $relationships as a dotted string, with or without
            // a record.  Record defaults to $this->getRecord(), relationships defaults to exploding $this->getName()
            $results = [];
            $record ??= $this->getRecord();
            $name = $relationships ?? $this->getName();
            $relationships = $this->getRelationshipStack($name);

            // if it wasn't actually relationship, just return the attribute wrapped in an array
            if ($this->isNoMoreRelationships($relationships)) {
                return (array) $record->getAttribute($name);
            }
        }

        // at this point we've taken care of any first time checks and are somewhere in our recursion stack, so
        // shift the next relationship off the stack ...

        $relationship = $this->shiftNextRelationship($relationships, $record);

        if ($this->isNoMoreRelationships($relationships)) {
            // if it was the last relationship in the stack, get the record(s) from it, push the attribute from
            // each one via Laravel's getAttribute() into an array, and return the array
            $attributes = [];

            foreach ($relationship->get() as $relatedRecord) {
                $attributes[] = $relatedRecord->getAttribute($this->getStackAttribute($relationships));
            }

            return $attributes;
        } else {
            // We haven't reached the end of the relationship stack, so shift the next relationship off the stack
            // then iterate through the related records for that relationship, recursing on each one, merging the
            // returned results into our results array

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

    /**
     * Return the singular Model at the end of a chain of one-to-one relationships, but without 'resolving' the
     * actual attribute.  So for 'comments.post.author.name', will return the Author model from the Comment's
     * related Post.
     *
     * @param  array|string|null  $relationships
     * @param  Model|null  $record
     * @return Model|null
     */
    public function getNestedRecord(array | string | null &$relationships = null, ?Model $record = null): ?Model
    {
        // first time checks, see if we need to set defaults
        if (! isset($relationships) || is_string($relationships)) {
            // Allow calling with either no args, or specifying the $relationships as a dotted string, with or without
            // a record.  Record will default to $this->getRecord(), relationships will default to exploding $this->getName()
            $record ??= $this->getRecord();
            $name = $relationships ?? $this->getName();
            $relationships = $this->getRelationshipStack($name);

            // if it's not really nested, just an attribute, return the original record
            if (! $this->isNoMoreRelationships($relationships)) {
                return $record;
            }
        }

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

    public function getNestedRelationshipQuery(string $name, Model $model, ?Builder $query = null, array | null &$relationships = null)
    {
        return $model::with($name);
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
     * @param  Model  $record
     * @param  array|string|null  $relationships
     * @return Relation|null
     */
    public function getLastRelationship(Model $record, array | string | null $relationships = null): ?Relation
    {
        // first time checks ...
        if (! isset($relationships) || is_string($relationships)) {
            $relationships = $this->getRelationshipStack($relationships ?? $this->getName());

            // it wasn't a dotted relationship, so bail with null
            if ($this->isNoMoreRelationships($relationships)) {
                return null;
            }
        }

        // shift the next relationship off the stack ...
        $relationship = $this->shiftNextRelationship($relationships, $record);

        if ($this->isNoMoreRelationships($relationships)) {
            // if it's the last relationship, we're done, return it

            return $relationship;
        } else {
            // if it's not the last one, get the related model, and recurse
            $record = $relationship->getRelated();

            return $this->getLastRelationship($record, $relationships);
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
    public function getLastRelationshipRecord(Model $record, array | string | null $relationships = null): ?Model
    {
        // first time checks ...
        if (! isset($relationships) || is_string($relationships)) {
            $relationships = $this->getRelationshipStack($relationships ?? $this->getName());

            // it wasn't a dotted relationship, so bail with null
            if ($this->isNoMoreRelationships($relationships)) {
                return null;
            }
        }

        // shift the next relationship off the stack ...
        $relationship = $this->shiftNextRelationship($relationships, $record);

        if ($this->isNoMoreRelationships($relationships)) {
            // if it's the last relationship, we're done, return the model

            return $record;
        } else {
            // if it's not the last one, get the related model, and recurse
            $record = $relationship->getRelated();

            return $this->getLastRelationship($record, $relationships);
        }
    }

    /**
     * @param  Builder  $query
     * @param  string  $column
     * @return Builder
     */
    public function getNestedOrderBy(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        return $query->orderBy(
            $this->getNestedRelationExistenceQuery($query, $column),
            $direction
        );
    }

    /**
     * This probably belongs somewhere else, wasn't sure where to put it.  Used from anywhere that builds am orderBy()
     * query potentially from a nested relationship, like 'post.author.name' on a Comments table.  Returns a nested
     * set of relationshipExistenceQueries, with each successive one being in the 'column' arg of the previous.  Works
     * for non nested names as well.
     *
     * Called as ...
     *
     * $query->orderBy(
     *     $this->getNestedRelationExistenceQuery($query, 'post.author.name'),
     *     'asc'
     * );
     *
     * Without doing this recursively, can't order on anything more than a single dot, so (say) 'post.title' works,
     * but 'post.author.name' doesn't, because the first relationship isn't included in the query.
     *
     * @param  Builder  $query
     * @param    $name
     * @param  array|null  $relationships
     * @return string|\Illuminate\Database\Query\Builder
     */
    protected function getNestedRelationExistenceQuery(Builder $query, $name, ?array $relationships = null): string | \Illuminate\Database\Query\Builder
    {
        // first time check, build the relationship stack
        if (! isset($relationships)) {
            $relationships = $this->getRelationshipStack($name);
        }

        if ($this->isNoMoreRelationships($relationships)) {
            // if no more relationships, return the attribute itself
            return $this->getStackAttribute($relationships);
        } else {
            // if there are relationships, get the model from the current query, and shift the next
            // relationship off the stack
            $record = $query->getModel();
            $relationship = $this->shiftNextRelationship($relationships, $record);

            // get the parent query for the relationship ...
            $parentQuery = $relationship->getRelated()::query();

            // now call getRelationExistenceQuery, with a recursive call to ourselves as the column, which will then
            // either be the attribute itself, or another recursive getRelationExistenceQuery.
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            return $relationship
                ->getRelationExistenceQuery(
                    $parentQuery,
                    $query,
                    [
                        $relationship->getRelationName() => $this->getNestedRelationExistenceQuery(
                            $parentQuery,
                            $name,
                            $relationships
                        ),
                    ],
                )
                ->applyScopes()
                ->getQuery();
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
            return $this->getInverseRelationships(
                $name,
                $record,
                $this->getRelationshipStack($name),
                [$record->getKeyName()]
            ) ?? null;
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

                array_unshift($reverse, $inverseRelationship);

                return $this->getInverseRelationships($name, $record, $relationships, $reverse, $relationshipName);
            }
        }
    }

    /**
     * Builds a nested query with a closure callback.  So calling this on just an attribute would just yield
     * the given closure  ...
     *
     * $this->getNestedQuery($query, 'name', $record, fn ($query) => $query->where(['name' => 'smith']))
     *
     * ... yields ...
     *
     * $query->where(['name' => 'smith'])
     *
     * ... but calling it on a dotted chain ...
     *
     * $this->getNestedQuery($query, 'post.author.name', $record, fn ($query) => $query->where(['name' => 'smith']))
     *
     * ... yields ...
     *
     * $query->whereHas(
     *     'post',
     *     fn (Builder $query) => $query->whereHas(
     *         'author',
     *          fn ($query) => $query->where(['name' => 'smith'])
     *     )
     *  )
     *
     * @param  Builder  $query
     * @param  array | string  $column
     * @param  Model  $record
     * @param  Closure  $callback
     * @param  array | null  $relationships
     * @return Builder
     */
    public function getNestedQuery(Builder $query, array | string $column, Model $record, Closure $callback, ?array $relationships = null): Builder
    {
        // first time check
        if (! isset($relationships)) {
            $relationships = is_array($column) ? $column : $this->getRelationshipStack($column);

            // if it's not nested, just return the callback
            if ($this->isNoMoreRelationships($relationships)) {
                return $this->evaluate($callback, ['query' => $query]);
            }

            $relationshipName = '';
        }

        $relationship = $this->shiftNextRelationship($relationships, $record, $relationshipName);
        $record = $relationship->getRelated();

        if ($this->isNoMoreRelationships($relationships)) {
            // we're at the end of the relationships, so return a whereHas with the closure callback,
            // which will unwind up the recursion stack
            return $query->whereHas(
                $relationshipName,
                fn (Builder $nestedQuery) => $this->evaluate($callback, ['query' => $nestedQuery])
            );
        } else {
            // more relationships to go, so return a whereHas with a recursive call as the callback closure
            return $query->whereHas(
                $relationshipName,
                fn (Builder $nestedQuery) => $this->getNestedQuery($nestedQuery, $column, $record, $callback, $relationships)
            );
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
