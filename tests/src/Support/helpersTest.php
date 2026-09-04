<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\View\ComponentAttributeBag;

use function Filament\get_authorization_response;
use function Filament\Support\apply_search_constraint;
use function Filament\Support\generate_search_column_expression;
use function Filament\Support\is_database_driver_supported;
use function Filament\Support\prepare_inherited_attributes;

uses(TestCase::class);

it('will prepare attributes', function (): void {
    $bag = new ComponentAttributeBag([
        'style' => 'color:red',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'style' => 'color:red',
    ]);
});

it('will prepare Alpine attributes', function (): void {
    $bag = new ComponentAttributeBag([
        'x-data' => '{foo:bar}',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'x-data' => '{foo:bar}',
    ]);
});

it('will prepare data attributes', function (): void {
    $bag = new ComponentAttributeBag([
        'data-foo' => 'bar',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'data-foo' => 'bar',
    ]);
});

it('preserves numeric keys in `prepare_inherited_attributes()`', function (array $originalAttributes, array $expectedAttributes): void {
    $bag = new ComponentAttributeBag($originalAttributes);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe($expectedAttributes);
})->with([
    'integer key' => [
        [1 => 'integer'],
        ['integer', 'integer'],
    ],
    'numeric string key' => [
        ['01' => 'numeric string'],
        ['01' => 'numeric string'],
    ],
]);

it('can handle policy being an object when method does not exist', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    get_authorization_response('edit', Ticket::class);
})->throws(Exception::class, 'Strict authorization mode is enabled, but no [edit()] method was found on [Filament\Tests\Fixtures\Policies\TicketPolicy].');

it('will generate a JSON search column expression for MySQL', function (): void {
    $column = 'data->name';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('mysql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new MySqlGrammar($databaseConnection);

    $databaseConnection->shouldReceive('getQueryGrammar')->andReturn($grammar);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(json_extract(`data`, '$.\"name\"'))");
});

it('will generate a JSON search column expression for Postgres', function (): void {
    $column = 'data->name';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->>'name'::text)");
});

it('will generate a nested JSON search column expression for Postgres', function (): void {
    $column = 'data->name->value->en';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->'name'->'value'->>'en'::text)");
});

it('will generate a column expression for Postgres with colons in the table name', function (string $column, string $expectedExpression): void {
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getTablePrefix')->andReturn('');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $actualExpression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($actualExpression->getValue($grammar))
        ->toBe($expectedExpression);
})
    ->with([
        ['blog:posts.title', 'lower("blog:posts"."title"::text)'],
        ['blog:posts:comments.author.name', 'lower("blog:posts:comments"."author"."name"::text)'],
    ]);

it('will generate a search column expression for Postgres with a table prefix', function (string $column, string $expectedExpression): void {
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getTablePrefix')->andReturn('app_');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $actualExpression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($actualExpression->getValue($grammar))
        ->toBe($expectedExpression);
})
    ->with([
        ['posts.title', 'lower("app_posts"."title"::text)'],
        ['posts.data->name', "lower(\"app_posts\".\"data\"->>'name'::text)"],
    ]);

it('will generate a JSON search column expression for Postgres with explicit ->> operator', function (): void {
    $column = 'data->>name';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->>'name'::text)");
});

it('will generate a nested JSON search column expression for Postgres with explicit ->> operator on the last segment', function (): void {
    $column = 'data->name->>ar';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->'name'->>'ar'::text)");
});

it('will generate a JSON search column expression for Postgres with explicit ->> operator and simple key', function (): void {
    $column = 'name->>\'en\'';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"name\"->>'en'::text)");
});

it('uses Filament search expressions for recognized database drivers', function (): void {
    $query = Ticket::query();

    $expectedSearchColumnExpression = match ($query->getConnection()->getDriverName()) {
        'pgsql' => 'lower("name"::text)',
        default => 'lower(name)',
    };

    $returnedQuery = apply_search_constraint(
        $query,
        'name',
        '%TeSt%',
        isSearchForcedCaseInsensitive: true,
        boolean: 'or',
        isInverse: true,
    );

    $where = $query->getQuery()->wheres[0];

    expect($returnedQuery)
        ->toBe($query)
        ->and($where['type'])
        ->toBe('Basic')
        ->and($where['column']->getValue($query->getQuery()->getGrammar()))
        ->toBe($expectedSearchColumnExpression)
        ->and($where['operator'])
        ->toBe('like')
        ->and($where['value'])
        ->toBe('%test%')
        ->and($where['boolean'])
        ->toBe('or not');
});

it('recognizes supported drivers with `is_database_driver_supported()`', function (string $driver, bool $isSupported): void {
    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->once()->andReturn($driver);

    expect(is_database_driver_supported($databaseConnection))->toBe($isSupported);
})->with([
    'MariaDB' => ['mariadb', true],
    'MySQL' => ['mysql', true],
    'PostgreSQL' => ['pgsql', true],
    'SQLite' => ['sqlite', true],
    'SQL Server' => ['sqlsrv', true],
    'MongoDB' => ['mongodb', false],
]);

it('can use `apply_search_constraint()` without applying a search collation', function (): void {
    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getTablePrefix')->andReturn('');
    $databaseConnection->shouldNotReceive('getConfig');

    $grammar = new PostgresGrammar($databaseConnection);
    $baseQuery = new QueryBuilder($databaseConnection, $grammar, new Processor);
    $query = (new EloquentBuilder($baseQuery))->setModel(new Ticket);

    apply_search_constraint(
        $query,
        'tickets.Name',
        '%TeSt%',
        shouldApplySearchCollation: false,
    );

    $where = $query->getQuery()->wheres[0];

    expect($where['column']->getValue($grammar))
        ->toBe('lower("tickets"."Name"::text)')
        ->and($where['value'])
        ->toBe('%test%');
});

it('uses `whereLike()` for unrecognized database drivers', function (): void {
    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->once()->andReturn('mongodb');

    $baseQuery = new class($databaseConnection, new Grammar($databaseConnection), new Processor) extends QueryBuilder
    {
        /** @var array<mixed> */
        public array $whereLikeArguments = [];

        public function whereLike($column, $value, $caseSensitive = false, $boolean = 'and', $not = false)
        {
            $this->whereLikeArguments = [$column, $value, $caseSensitive, $boolean, $not];

            return $this;
        }
    };

    $query = (new EloquentBuilder($baseQuery))->setModel(new Ticket);

    $returnedQuery = apply_search_constraint(
        $query,
        'profile.name',
        '%Te_st%',
        boolean: 'or',
        isInverse: true,
    );

    expect($returnedQuery)
        ->toBe($query)
        ->and($baseQuery->whereLikeArguments)
        ->toBe(['profile.name', '%Te_st%', false, 'or', true]);
});
