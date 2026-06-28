<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tests\Fixtures\Livewire\RelationshipRecordBelongsToTooltipTable;
use Filament\Tests\Fixtures\Livewire\RelationshipRecordDistinctListTable;
use Filament\Tests\Fixtures\Livewire\RelationshipRecordIconColumnTable;
use Filament\Tests\Fixtures\Livewire\RelationshipRecordLimitedListTable;
use Filament\Tests\Fixtures\Livewire\RelationshipRecordPostTable;
use Filament\Tests\Fixtures\Livewire\RelationshipRecordUserTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Arr;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('relationship record resolution', function (): void {
    it('can resolve relationship records aligned with many-to-many state', function (): void {
        $teamAlpha = Team::factory()->create(['name' => 'Alpha']);
        $teamBeta = Team::factory()->create(['name' => 'Beta']);

        $user = User::factory()->create();
        $user->teams()->attach([$teamAlpha->id, $teamBeta->id]);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user, $teamAlpha, $teamBeta): bool {
                $column->record($user);

                $state = Arr::wrap($column->getState());
                $relationshipRecords = $column->getRelationshipRecords();

                expect($state)->toHaveCount(2)
                    ->and(collect($state)->sort()->values()->all())->toBe(['Alpha', 'Beta'])
                    ->and($relationshipRecords)->toHaveCount(2)
                    ->and(collect($relationshipRecords)->pluck('id')->sort()->values()->all())
                    ->toBe(collect([$teamAlpha->id, $teamBeta->id])->sort()->values()->all());

                return true;
            });
    });

    it('can resolve a single relationship record for belongs-to columns', function (): void {
        $team = Team::factory()->create(['name' => 'Alpha']);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('team.name', function (TextColumn $column) use ($user, $team): bool {
                $column->record($user);

                expect($column->getState())->toBe('Alpha')
                    ->and($column->getRelationshipRecords())->toHaveCount(1)
                    ->and($column->getRelationshipRecord()?->is($team))->toBeTrue();

                return true;
            });
    });

    it('returns `null` from `getRelationshipRecord()` when multiple related records exist', function (): void {
        $user = User::factory()->create();
        $user->teams()->attach(Team::factory()->count(2)->create()->pluck('id'));

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user): bool {
                $column->record($user);

                expect($column->getRelationshipRecords())->toHaveCount(2)
                    ->and($column->getRelationshipRecord())->toBeNull();

                return true;
            });
    });

    it('returns an empty relationship record list for non-relationship columns', function (): void {
        $user = User::factory()->create(['name' => 'Jane']);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('name', function (TextColumn $column) use ($user): bool {
                $column->record($user);

                expect($column->getState())->toBe('Jane')
                    ->and($column->getRelationshipRecords())->toBe([])
                    ->and($column->getRelationshipRecord())->toBeNull();

                return true;
            });
    });

    it('excludes related records with blank relationship state', function (): void {
        $teamWithName = Team::factory()->create(['name' => 'Visible']);
        $teamWithoutName = Team::factory()->create(['name' => '']);

        $user = User::factory()->create();
        $user->teams()->attach([$teamWithName->id, $teamWithoutName->id]);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user, $teamWithName): bool {
                $column->record($user);

                expect(Arr::wrap($column->getState()))->toBe(['Visible'])
                    ->and($column->getRelationshipRecords())->toHaveCount(1)
                    ->and($column->getRelationshipRecords()[0]->is($teamWithName))->toBeTrue();

                return true;
            });
    });

    it('keeps relationship records aligned when using `distinctList()`', function (): void {
        $teamAlpha = Team::factory()->create(['name' => 'Shared']);
        $teamBeta = Team::factory()->create(['name' => 'Shared']);
        $teamGamma = Team::factory()->create(['name' => 'Unique']);

        $user = User::factory()->create();
        $user->teams()->attach([$teamAlpha->id, $teamBeta->id, $teamGamma->id]);

        livewire(RelationshipRecordDistinctListTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user, $teamAlpha, $teamGamma): bool {
                $column->record($user);

                $state = Arr::wrap($column->getState());
                $relationshipRecords = $column->getRelationshipRecords();

                expect($state)->toHaveCount(2)
                    ->and($relationshipRecords)->toHaveCount(2)
                    ->and($column->getColor($state[0], $relationshipRecords[0]))->toBe("team-{$teamAlpha->id}")
                    ->and($column->getColor($state[1], $relationshipRecords[1]))->toBe("team-{$teamGamma->id}");

                return true;
            });
    });

    it('can resolve nested relationship records', function (): void {
        $team = Team::factory()->create(['name' => 'Alpha']);
        $user = User::factory()->create(['team_id' => $team->id]);
        $post = Post::factory()->create(['author_id' => $user->id]);

        livewire(RelationshipRecordPostTable::class, ['postId' => $post->id])
            ->assertTableColumnExists('author.team.name', function (TextColumn $column) use ($post, $team): bool {
                $column->record($post);

                expect($column->getState())->toBe('Alpha')
                    ->and($column->getRelationshipRecord()?->is($team))->toBeTrue()
                    ->and($column->getColor('Alpha', $column->getRelationshipRecord()))->toBe('warning');

                return true;
            });
    });
});

describe('relationship record closure injection', function (): void {
    it('can access the relationship record in `color()` closures', function (): void {
        $teamAlpha = Team::factory()->create(['name' => 'Alpha']);
        $teamBeta = Team::factory()->create(['name' => 'Beta']);

        $user = User::factory()->create();
        $user->teams()->attach([$teamAlpha->id, $teamBeta->id]);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user): bool {
                $column->record($user);

                $state = Arr::wrap($column->getState());
                $relationshipRecords = $column->getRelationshipRecords();

                $colors = collect($state)
                    ->map(fn (string $stateItem, int $index): ?string => $column->getColor($stateItem, $relationshipRecords[$index]))
                    ->sort()
                    ->values()
                    ->all();

                expect($colors)->toBe(['danger', 'success']);

                return true;
            });
    });

    it('can access the relationship record in `tooltip()` closures', function (): void {
        $team = Team::factory()->create(['name' => 'Alpha']);

        $user = User::factory()->create();
        $user->teams()->attach($team);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user, $team): bool {
                $column->record($user);

                expect($column->getTooltip('Alpha', $column->getRelationshipRecord()))
                    ->toBe("Team #{$team->id}");

                return true;
            });
    });

    it('can access the relationship record in state-based `url()` closures', function (): void {
        $team = Team::factory()->create(['name' => 'Alpha']);

        $user = User::factory()->create();
        $user->teams()->attach($team);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user, $team): bool {
                $column->record($user);

                expect($column->getUrl('Alpha', $column->getRelationshipRecords()[0]))
                    ->toBe("/teams/{$team->id}");

                return true;
            });
    });

    it('can access the relationship record in `formatStateUsing()` closures', function (): void {
        $team = Team::factory()->create(['name' => 'Alpha']);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('team.name', function (TextColumn $column) use ($user, $team): bool {
                $column->record($user);

                expect($column->formatState($team->name, $column->getRelationshipRecord()))
                    ->toBe("Alpha (#{$team->id})");

                return true;
            });
    });

    it('can access the relationship record in `icon()` and `color()` closures on icon columns', function (): void {
        $teamAlpha = Team::factory()->create(['name' => 'Alpha']);
        $teamBeta = Team::factory()->create(['name' => 'Beta']);

        $user = User::factory()->create();
        $user->teams()->attach([$teamAlpha->id, $teamBeta->id]);

        livewire(RelationshipRecordIconColumnTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('teams.name', function (IconColumn $column) use ($user, $teamAlpha, $teamBeta): bool {
                $column->record($user);

                $state = Arr::wrap($column->getState());
                $relationshipRecords = $column->getRelationshipRecords();

                $alphaIndex = collect($relationshipRecords)->search(fn (Team $team): bool => $team->is($teamAlpha));
                $betaIndex = collect($relationshipRecords)->search(fn (Team $team): bool => $team->is($teamBeta));

                expect($column->getIcon($state[$alphaIndex], $relationshipRecords[$alphaIndex]))->toBe(Heroicon::CheckCircle)
                    ->and($column->getIcon($state[$betaIndex], $relationshipRecords[$betaIndex]))->toBe(Heroicon::XCircle)
                    ->and($column->getColor($state[$alphaIndex], $relationshipRecords[$alphaIndex]))->toBe('success')
                    ->and($column->getColor($state[$betaIndex], $relationshipRecords[$betaIndex]))->toBe('danger');

                return true;
            });
    });
});

describe('relationship record rendering', function (): void {
    it('can render many-to-many text badges without errors', function (): void {
        $user = User::factory()->create();
        $user->teams()->attach(Team::factory()->count(2)->create());

        livewire(RelationshipRecordUserTable::class, ['userId' => $user->id])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$user]);
    });

    it('can render icon columns for relationship state without errors', function (): void {
        $user = User::factory()->create();
        $user->teams()->attach(Team::factory()->count(2)->create());

        livewire(RelationshipRecordIconColumnTable::class, ['userId' => $user->id])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$user]);
    });

    it('keeps relationship records aligned when `limitList()` slices rendered state', function (): void {
        $teamAlpha = Team::factory()->create(['name' => 'Alpha']);
        $teamBeta = Team::factory()->create(['name' => 'Beta']);

        $user = User::factory()->create();
        $user->teams()->attach([$teamAlpha->id, $teamBeta->id]);

        livewire(RelationshipRecordLimitedListTable::class, ['userId' => $user->id])
            ->assertSuccessful()
            ->assertTableColumnExists('teams.name', function (TextColumn $column) use ($user, $teamAlpha): bool {
                $column->record($user);

                $state = Arr::wrap($column->getState());
                $relationshipRecords = $column->getRelationshipRecords();

                expect($state)->toHaveCount(2)
                    ->and($relationshipRecords)->toHaveCount(2);

                $alphaIndex = collect($relationshipRecords)->search(fn (Team $team): bool => $team->is($teamAlpha));

                expect($column->getColor($state[$alphaIndex], $relationshipRecords[$alphaIndex]))->toBe('success')
                    ->and($column->getColor($state[1 - $alphaIndex], $relationshipRecords[1 - $alphaIndex]))->toBe('danger');

                return true;
            });
    });
});

describe('relationship record column-level injection', function (): void {
    it('can inject `relationshipRecord` by name for belongs-to column closures', function (): void {
        $team = Team::factory()->create(['name' => 'Alpha']);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(RelationshipRecordBelongsToTooltipTable::class, ['userId' => $user->id])
            ->assertTableColumnExists('team.name', function (TextColumn $column) use ($user, $team): bool {
                $column->record($user);

                expect($column->getTooltip($team->name))->toBe("Belongs to team {$team->id}");

                return true;
            });
    });
});
