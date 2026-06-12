<?php

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\prepare_inherited_attributes;

it('will prepare attributes', function () {
    $bag = new ComponentAttributeBag([
        'style' => 'color:red',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'style' => 'color:red',
    ]);
});

it('will prepare Alpine attributes', function () {
    $bag = new ComponentAttributeBag([
        'x-data' => '{foo:bar}',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'x-data' => '{foo:bar}',
    ]);
});

it('will prepare data attributes', function () {
    $bag = new ComponentAttributeBag([
        'data-foo' => 'bar',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'data-foo' => 'bar',
    ]);
});

it('will generate a JSON search column expression for MySQL', function () {
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

it('will generate a JSON search column expression for Postgres', function () {
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

it('will generate a nested JSON search column expression for Postgres', function () {
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

it('will generate a column expression for Postgres with colons in the table name', function (string $column, string $expectedExpression) {
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
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

it('will generate a JSON search column expression for Postgres with explicit ->> operator', function () {
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

it('will generate a nested JSON search column expression for Postgres with explicit ->> operator on the last segment', function () {
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

it('will generate a JSON search column expression for Postgres with explicit ->> operator and simple key', function () {
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
