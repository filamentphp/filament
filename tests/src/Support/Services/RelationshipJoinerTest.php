<?php

use Filament\Support\Services\RelationshipJoiner;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    // Add a raw order with a direction
    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints(
        $user->teams()->orderByRaw('role DESC')
    );

    expect($preparedQuery->toBase())
        ->distinct->toBeTrue()
        ->getColumns()->not->toContain([
            'role DESC',
        ])
        ->orders->toHaveCount(1)
        ->and($preparedQuery->toBase()->orders[0])
        ->type->toBe('Raw')
        ->sql->toBe('role DESC');
});

it('does not modify a `BelongsToMany` query without a pivot join', function (): void {
    $user = User::factory()->create();

    $relationship = new class(Team::query()->where('name', 'Test')->orderBy('name'), $user, 'team_user', 'user_id', 'team_id', 'id', 'id', 'teams') extends BelongsToMany
    {
        public function addConstraints(): void {}
    };

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

    expect($preparedQuery)
        ->toBe($relationship->getQuery())
        ->and($preparedQuery->toBase())
        ->joins->toBeNull()
        ->distinct->toBeFalse()
        ->getColumns()->toBe([])
        ->orders->toBe([
            [
                'column' => 'name',
                'direction' => 'asc',
            ],
        ]);
});

it('removes only the pivot join from a `BelongsToMany` query', function (): void {
    $user = User::factory()->create();
    $relationship = $user->teams();
    $query = $relationship->getQuery()->join('companies', 'companies.id', '=', 'teams.company_id');

    app(RelationshipJoiner::class)->removeJoinForRelationship($query, $relationship);

    expect($query->toBase()->joins)
        ->toHaveCount(1)
        ->and($query->toBase()->joins[0]->table)->toBe('companies');
});

describe('`getUnattachedQuery()`', function (): void {
    it('uses `newUnattachedQuery()` provided by an unsupported `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        Team::factory()->create(['name' => 'Attached']);
        $unattachedTeam = Team::factory()->create(['name' => 'Unattached']);
        $databaseConnection = Mockery::mock(Connection::class);
        $databaseConnection->shouldReceive('getDriverName')->andReturn('mongodb');

        $relationship = new class(Team::query(), $user, 'team_user', 'user_id', 'team_id', 'id', 'id', 'teams') extends BelongsToMany
        {
            public bool $createdUnattachedQuery = false;

            public Connection $databaseConnection;

            public function getConnection(): Connection
            {
                return $this->databaseConnection;
            }

            public function newUnattachedQuery(): EloquentBuilder
            {
                $this->createdUnattachedQuery = true;

                return $this->getRelated()->newQuery()->where('name', 'Unattached');
            }
        };
        $relationship->databaseConnection = $databaseConnection;

        expect(app(RelationshipJoiner::class)->getUnattachedQuery($relationship)->get())
            ->modelKeys()->toBe([$unattachedTeam->getKey()])
            ->and($relationship->createdUnattachedQuery)->toBeTrue();
    });

    it('uses a pivot subquery for a supported driver when the `BelongsToMany` relationship provides `newUnattachedQuery()`', function (): void {
        $user = User::factory()->create();
        $attachedTeam = Team::factory()->create();
        $unattachedTeam = Team::factory()->create();
        $user->teams()->attach($attachedTeam);

        $relationship = new class(Team::query(), $user, 'team_user', 'user_id', 'team_id', 'id', 'id', 'teams') extends BelongsToMany
        {
            public bool $createdUnattachedQuery = false;

            public function newUnattachedQuery(): EloquentBuilder
            {
                $this->createdUnattachedQuery = true;

                return $this->getRelated()->newQuery();
            }
        };

        expect(app(RelationshipJoiner::class)->getUnattachedQuery($relationship)->get())
            ->modelKeys()->toBe([$unattachedTeam->getKey()])
            ->and($relationship->createdUnattachedQuery)->toBeFalse();
    });
});
