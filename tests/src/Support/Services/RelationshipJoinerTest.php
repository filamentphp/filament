<?php

use Filament\Support\Services\RelationshipJoiner;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;

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

it('preserves related and pivot binding associations in either declaration order', function (string $pivotConstraint, bool $pivotFirst): void {
    $user = User::factory()->create();
    $excludedTeam = Team::factory()->create(['name' => 'Excluded']);
    $includedTeam = Team::factory()->create(['name' => 'Included']);
    $unattachedTeam = Team::factory()->create(['name' => 'Unattached']);
    $user->teams()->attach([$excludedTeam->getKey(), $includedTeam->getKey()], ['role' => 'member']);

    $isList = in_array($pivotConstraint, ['wherePivotIn', 'wherePivotNotIn']);
    $constrainRelated = static fn (BelongsToMany $relationship): BelongsToMany => $isList
        ? $relationship->whereNotIn('teams.name', ['Excluded', 'Also excluded'])
        : $relationship->where('teams.name', '!=', 'Excluded');
    $constrainPivot = static fn (BelongsToMany $relationship): BelongsToMany => match ($pivotConstraint) {
        'withPivotValue' => $relationship->withPivotValue('role', 'member'),
        'wherePivot' => $relationship->wherePivot('role', 'member'),
        'wherePivotIn' => $relationship->wherePivotIn('role', ['member', 'owner']),
        'wherePivotNotIn' => $relationship->wherePivotNotIn('role', ['blocked', 'banned']),
    };
    $constrain = static fn (BelongsToMany $relationship): BelongsToMany => $pivotFirst
        ? $constrainRelated($constrainPivot($relationship))
        : $constrainPivot($constrainRelated($relationship));

    expect($constrain($user->teams())->get()->modelKeys())->toBe([$includedTeam->getKey()]);

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints(
        $constrain(Relation::noConstraints(fn (): BelongsToMany => $user->teams())),
    );
    $pivotBindings = match ($pivotConstraint) {
        'wherePivotIn' => ['member', 'owner'],
        'wherePivotNotIn' => ['blocked', 'banned'],
        default => ['member'],
    };
    $relatedBindings = $isList ? ['Excluded', 'Also excluded'] : ['Excluded'];

    expect($preparedQuery->getRawBindings()['join'])->toBe($pivotBindings)
        ->and($preparedQuery->getRawBindings()['where'])->toBe($relatedBindings)
        ->and($preparedQuery->getQuery()->joins[0]->getBindings())->toBe($pivotBindings)
        ->and($preparedQuery->orderBy('teams.id')->get()->modelKeys())->toBe([$includedTeam->getKey(), $unattachedTeam->getKey()]);
})->with(['withPivotValue', 'wherePivot', 'wherePivotIn', 'wherePivotNotIn'])->with([false, true]);

it('preserves ranges, nested groups, scalar types and binding-free clauses in either order', function (bool $pivotFirst): void {
    $user = User::factory()->create();
    $includedTeam = Team::factory()->create(['name' => 'Included', 'budget' => 15]);
    Team::factory()->create(['name' => 'Excluded', 'budget' => 15]);
    Team::factory()->create(['name' => 'Out of range', 'budget' => 30]);
    $user->teams()->attach($includedTeam, ['role' => 'member']);

    $constrainRelated = static fn (BelongsToMany $relationship): BelongsToMany => $relationship
        ->where('teams.name', '!=', 'Excluded')
        ->whereBetween('teams.budget', [10, 20])
        ->where(static fn (EloquentBuilder $query): EloquentBuilder => $query
            ->where('teams.name', '!=', 'Nested')
            ->orWhere(static fn (EloquentBuilder $query): EloquentBuilder => $query->where('teams.budget', 99)->whereNotIn('teams.name', ['Never', 'Also never'])))
        ->whereColumn('teams.id', 'teams.id')
        ->whereIntegerInRaw('teams.budget', [15, 16])
        ->whereNotIn('teams.id', [])
        ->whereNotNull('teams.name');
    $constrainPivot = static fn (BelongsToMany $relationship): BelongsToMany => $relationship
        ->wherePivotBetween('team_id', [$includedTeam->getKey(), $includedTeam->getKey() + 5])
        ->wherePivot('user_id', '>=', $user->getKey())
        ->wherePivotNotBetween('user_id', [$user->getKey() + 1, $user->getKey() + 3])
        ->wherePivot('user_id', '!=', false)
        ->wherePivotNull('created_at');
    $relationship = Relation::noConstraints(fn (): BelongsToMany => $user->teams());
    $relationship = $pivotFirst ? $constrainRelated($constrainPivot($relationship)) : $constrainPivot($constrainRelated($relationship));
    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);
    $pivotBindings = [$includedTeam->getKey(), $includedTeam->getKey() + 5, $user->getKey(), $user->getKey() + 1, $user->getKey() + 3, false];

    expect($preparedQuery->getRawBindings()['join'])->toBe($pivotBindings)
        ->and($preparedQuery->getRawBindings()['where'])->toBe(['Excluded', 10, 20, 'Nested', 99, 'Never', 'Also never'])
        ->and($preparedQuery->getQuery()->joins[0]->getBindings())->toBe($pivotBindings)
        ->and($preparedQuery->get()->modelKeys())->toBe([$includedTeam->getKey()]);
})->with([false, true]);

it('inserts pivot bindings after existing first join bindings and before later join bindings', function (): void {
    $user = User::factory()->create();
    $includedTeam = Team::factory()->create(['name' => 'Included']);
    Team::factory()->create(['name' => 'Excluded']);
    $user->teams()->attach($includedTeam, ['role' => 'member']);
    $relationship = $user->teams();
    $query = $relationship->getQuery()->getQuery();
    $query->joins[0]->where('team_user.role', '!=', 'first join');
    $query->addBinding(['first join'], 'join');
    $relationship
        ->joinSub(Team::query()->where('name', '!=', 'second table'), 'other_teams', static fn (JoinClause $join): JoinClause => $join
            ->on('other_teams.id', '=', 'teams.id')
            ->where('other_teams.name', '!=', 'second join'))
        ->where('teams.name', '!=', 'Excluded')
        ->wherePivot('role', 'member');

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

    expect($preparedQuery->getRawBindings()['join'])->toBe(['first join', $user->getKey(), 'member', 'second table', 'second join'])
        ->and($preparedQuery->getRawBindings()['where'])->toBe(['Excluded'])
        ->and($query->joins[0]->getBindings())->toBe(['first join', $user->getKey(), 'member'])
        ->and($query->joins[1]->getBindings())->toBe(['second join'])
        ->and($preparedQuery->get()->modelKeys())->toBe([$includedTeam->getKey()]);
});

it('rejects unsupported binding associations before changing the query', function (string $constraint): void {
    $user = User::factory()->create();
    $relationship = Relation::noConstraints(fn (): BelongsToMany => $user->teams())->wherePivot('role', 'member');

    if ($constraint === 'derived first join') {
        $relationship->getQuery()->getQuery()->joins = [];
        $relationship->joinSub($user->getConnection()->table('team_user')->where('user_id', $user->getKey()), 'team_user', 'teams.id', '=', 'team_user.team_id');
    } else {
        match ($constraint) {
            'raw' => $relationship->whereRaw('teams.name != ?', ['Excluded']),
            'nested raw' => $relationship->where(static fn (EloquentBuilder $query): EloquentBuilder => $query->whereRaw('teams.name != ?', ['Excluded'])),
            'subquery list' => $relationship->whereIn('teams.id', static fn (Builder $query): Builder => $query->select('id')->from('teams')->where('name', '!=', 'Excluded')),
        };
    }

    $originalSql = $relationship->toSql();
    $originalBindings = $relationship->getRawBindings();

    expect(fn () => app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship))->toThrow(LogicException::class)
        ->and($relationship->toSql())->toBe($originalSql)
        ->and($relationship->getRawBindings())->toBe($originalBindings);
})->with(['raw', 'nested raw', 'subquery list', 'derived first join']);

it('leaves binding-free pivot constraints and other relationships intact', function (): void {
    $user = User::factory()->create();
    $relationship = $user->posts()->where('title', '!=', 'Excluded');
    $sql = $relationship->toSql();
    $bindings = $relationship->getBindings();

    expect(app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship))
        ->toSql()->toBe($sql)
        ->getBindings()->toBe($bindings);

    $preparedQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints(
        Relation::noConstraints(fn (): BelongsToMany => $user->teams())
            ->where('teams.name', '!=', 'Excluded')
            ->wherePivotNull('role'),
    );

    expect($preparedQuery->getRawBindings()['join'])->toBe([])
        ->and($preparedQuery->getRawBindings()['where'])->toBe(['Excluded']);
});
