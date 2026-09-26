---
title: Database compatibility
---

## Introduction

Filament's built-in database support covers MariaDB (`mariadb`), MySQL (`mysql`), PostgreSQL (`pgsql`), SQLite (`sqlite`), and SQL Server (`sqlsrv`). Filament sometimes uses database-specific SQL for these drivers when Laravel's query builder does not provide the behavior that a feature needs.

Other database drivers can support these features through methods on their Eloquent query builder and relationship classes. Filament does not require an adapter, interface, or package-specific integration. It uses Laravel's existing query builder methods where possible and checks for the additional methods described below before falling back to SQL. You only need to implement the methods used by the features that your driver supports. Filament does not call these additional methods for its built-in supported drivers.

The Filament-specific methods must be real public methods on the custom Eloquent builder or relationship class. Filament discovers them using `method_exists()`, so builder macros and methods that are only available through `__call()` are not detected.

## Searching text

For supported drivers, Filament preserves its database-specific handling for case-insensitive searches, JSON paths, collations, and PostgreSQL text casting. For any other driver, Filament's built-in table searches, relationship option searches, action record searches, resource global search, and query builder text operators use Laravel's `whereLike()` method:

```php
use Illuminate\Contracts\Database\Query\Expression;

public function whereLike(
    string | Expression $column,
    string $value,
    bool $caseSensitive = false,
    string $boolean = 'and',
    bool $not = false,
): static
{
    // Apply the search constraint to the query.
}
```

This is a standard Laravel query builder method rather than a Filament-specific extension. Eloquent normally forwards the call to the underlying query builder. Filament calls it without checking `method_exists()` and passes `false` for `$caseSensitive`. The driver implementation should interpret `%` and `_` as multi-character and single-character wildcards respectively, honor the boolean and negation arguments, apply a case-insensitive search, and return the builder.

## Ordering by a relationship

Filament normally orders records by a relationship attribute using a correlated SQL subquery. A custom Eloquent builder can implement the operation directly instead:

```php
public function orderByRelation(
    string $relation,
    string $column,
    string $direction = 'asc',
): static
{
    // Apply the relationship ordering to the query.
}
```

The `$relation` may be a dot-notated nested relationship path. The implementation should order the parent records by the related `$column` in the requested direction and return the builder. Filament uses this method when sorting table relationship columns and ordering table groups by relationship attributes.

## Ordering by a relationship aggregate

Filament loads relationship aggregates using Laravel's `withAvg()`, `withCount()`, `withExists()`, `withMax()`, `withMin()`, and `withSum()` methods. SQL drivers can then order by the generated aggregate alias. Drivers that cannot order by these hydrated aliases can implement the aggregate and ordering together:

```php
use Closure;
use Illuminate\Contracts\Database\Query\Expression;

public function orderByRelationAggregate(
    string $relation,
    string | Expression $column,
    string $function,
    string $direction = 'asc',
    ?Closure $callback = null,
): static
{
    // Apply the relationship aggregate ordering to the query.
}
```

Filament calls this method when sorting table columns configured with `avg()`, `counts()`, `exists()`, `max()`, `min()`, or `sum()`. The `$function` is `avg`, `count`, `exists`, `max`, `min`, or `sum`; `$column` is `*` for `count` and `exists`. If provided, the `$callback` must be applied to the related query before calculating the aggregate. The implementation should order the parent records by the aggregate in the requested direction and return the builder.

## Filtering by a relationship aggregate

Filament normally filters by a relationship aggregate using a correlated SQL subquery. A custom Eloquent builder can implement the aggregate comparison directly instead:

```php
use Closure;

public function whereRelationAggregate(
    string $relation,
    string $column,
    string $function,
    string $operator,
    float $value,
    ?Closure $callback = null,
): static
{
    // Apply the relationship aggregate constraint to the query.
}
```

Filament uses this method for query builder number constraints that aggregate a relationship column. The `$function` is `avg`, `max`, `min`, or `sum`, and `$operator` is the comparison operator selected by the constraint. If provided, the `$callback` must be applied to the related query before calculating the aggregate. The implementation should compare the aggregate with `$value`, constrain the parent query, and return the builder.

## Querying unattached relationship records

Filament normally finds records that can be attached to a `BelongsToMany` relationship using a pivot-table subquery. A custom `BelongsToMany` relationship without a SQL pivot table can provide this query directly:

```php
use Illuminate\Database\Eloquent\Builder;

public function newUnattachedQuery(): Builder
{
    // Return a query for related records that are not attached to the parent.
}
```

The method should return a new Eloquent query for the related model, preserve any relevant relationship constraints, exclude records already attached to the current parent record, and remain independently modifiable by Filament. Filament uses this method in table select fields that exclude already-attached records, including attach actions configured with `tableSelect()`.
