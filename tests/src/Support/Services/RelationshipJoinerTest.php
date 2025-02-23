<?php

use Filament\Support\Services\RelationshipJoiner;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Query\Expression;

uses(TestCase::class);

it('can prepare query for no constraints for a BelongsToMany relationship', function (): void {
    $user = User::factory()->create();

    expect($user->teams()->toBase())
        ->distinct->toBeFalse()
        ->getColumns()->toBe([])
        ->orders->toBeNull();

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($user->teams());

    expect($preparedQuery->toBase())
        ->distinct->toBeTrue()
        ->getColumns()->toBe(['teams.*'])
        ->orders->toBeNull();

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints(
        $user
            ->teams()
            ->orderBy('id')
            ->orderBy((new Team)->qualifyColumn('name'))
            ->orderBy('team_user.role')
    );

    expect($preparedQuery->toBase())
        ->distinct->toBeTrue()
        ->getColumns()->toBe([
            (new Team)->qualifyColumn('*'), // Default select...
            'id', // Select without a qualified table also included just to be sure...
            // Select for `team.name` not included as that is already included in the `team.*`...
            'team_user.role', // Select for a qualitified other table included...
        ])
        ->orders->toBe([
            [
                'column' => 'id',
                'direction' => 'asc',
            ],
            [
                'column' => 'teams.name',
                'direction' => 'asc',
            ],
            [
                'column' => 'team_user.role',
                'direction' => 'asc',
            ],
        ]);

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints(
        $user->teams()->orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 2 END")
    );

    expect($preparedQuery->toBase())
        ->distinct->toBeTrue()
        ->getColumns()->toBe([
            (new Team)->qualifyColumn('*'),
            "CASE WHEN role = 'admin' THEN 1 ELSE 2 END", // Select added from `orderByRaw`...
        ])
        ->orders->toBe([
            [
                'type' => 'Raw',
                'sql' => "CASE WHEN role = 'admin' THEN 1 ELSE 2 END",
            ],
        ]);

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints(
        $user->teams()->orderBy(new Expression("CASE WHEN role = 'some_other_role' THEN 1 ELSE 2 END"))
    );

    expect($preparedQuery->toBase())
        ->distinct->toBeTrue()
        ->getColumns()->toBe([
            (new Team)->qualifyColumn('*'),
            "CASE WHEN role = 'some_other_role' THEN 1 ELSE 2 END", // Select added from `orderByRaw`...
        ])
        ->orders->toHaveCount(1)
        ->and($preparedQuery->toBase()->orders[0])
        ->column->getValue($user->teams()->getGrammar())->toBe("CASE WHEN role = 'some_other_role' THEN 1 ELSE 2 END")
        ->direction->toBe('asc');
});
