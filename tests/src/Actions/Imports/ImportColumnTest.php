<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

describe('relationship columns', function (): void {
    $makeColumn = static function (User $user, string $name = 'teams'): ImportColumn {
        $importer = Mockery::mock(Importer::class);
        $importer->shouldReceive('getRecord')->andReturn($user);

        return ImportColumn::make($name)->importer($importer);
    };

    it('validates unique lookup values without changing the resolved or attached records', function (string $resolution, bool $hasMissingValue) use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'Editorial', 'slug' => 'editorial']);
        $secondTeam = Team::factory()->create(['name' => 'Research', 'slug' => 'research']);
        $unrelatedTeam = Team::factory()->create(['name' => 'Support', 'slug' => 'support']);
        $user->teams()->attach($unrelatedTeam);

        $firstValue = ($resolution === 'primary key') ? $firstTeam->getKey() : $firstTeam->name;
        $secondValue = match ($resolution) {
            'primary key' => $secondTeam->getKey(),
            'columns', 'column callback' => $secondTeam->slug,
            default => $secondTeam->name,
        };
        $missingValue = ($resolution === 'primary key') ? $unrelatedTeam->getKey() + 100 : 'Missing';
        $state = [(string) $firstValue, (string) $firstValue, (string) $secondValue];

        if ($hasMissingValue) {
            $state[] = (string) $missingValue;
        }

        $resolveUsing = match ($resolution) {
            'primary key' => null,
            'title' => 'name',
            'columns' => ['name', 'slug'],
            'column callback' => static function (array $state) use ($firstValue): array {
                expect($state[0])->toBe((string) $firstValue)
                    ->and($state[1])->toBe((string) $firstValue);

                return ['name', 'slug'];
            },
            'collection callback' => static function (array $state) use ($firstValue): Collection {
                expect($state[0])->toBe((string) $firstValue)
                    ->and($state[1])->toBe((string) $firstValue);

                return Team::query()->whereIn('name', $state)->get();
            },
        };

        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: $resolveUsing);
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);

        expect($validator->fails())->toBe($hasMissingValue)
            ->and($validator->errors()->get('teams'))->toBe($hasMissingValue ? [__('validation.exists', ['attribute' => 'teams'])] : [])
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);

        if (! $hasMissingValue) {
            $column->saveRelationships($state);
        }

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing($hasMissingValue
            ? [$unrelatedTeam->getKey()]
            : [$unrelatedTeam->getKey(), $firstTeam->getKey(), $secondTeam->getKey()]);
    })->with(['primary key', 'title', 'columns', 'column callback', 'collection callback'])->with([false, true]);

    it('requires every lookup value to match even when database values are duplicated', function (string $resolution, string $input, bool $hasMissingValue) use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'A', 'slug' => 'first']);
        $secondTeam = Team::factory()->create(['name' => 'A', 'slug' => 'second']);
        $calls = 0;
        $resolveUsing = match ($resolution) {
            'title' => 'name',
            'columns' => ['name', 'slug'],
            'column callback' => static function (array $state) use ($input, &$calls): array {
                $calls++;
                expect($state)->toBe(explode(',', $input));

                return ['name', 'slug'];
            },
        };
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: $resolveUsing);
        $state = $column->castState($input);
        $records = $column->resolveRelatedRecords($state);

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->fails())->toBe($hasMissingValue)
            ->and($column->resolveRelatedRecords($state))->toBe($records)
            ->and($records->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);

        if (! $hasMissingValue) {
            $column->saveRelationships($state);
        }

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing($hasMissingValue ? [] : [$firstTeam->getKey(), $secondTeam->getKey()])
            ->and($calls)->toBe($resolution === 'column callback' ? 1 : 0);
    })->with(['title', 'columns', 'column callback'])->with([
        'single value' => ['A', false],
        'repeated value' => ['A,A,A', false],
        'missing value' => ['A,Missing', true],
        'repeated and missing values' => ['A,A,Missing', true],
    ]);

    it('preserves the selected columns in SQL Server relationship coverage probes', function () use ($makeColumn): void {
        $connection = app(SqlServerConnection::class, ['pdo' => $this->app['db']->connection()->getPdo()]);
        $this->app['config']->set('database.connections.import_sqlsrv', ['driver' => 'sqlsrv']);
        $this->app['db']->extend('sqlsrv', static fn (): SqlServerConnection => $connection);
        $user = app(User::class)->setConnection('import_sqlsrv');
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(
            static fn (): BelongsToMany => $user->teams()->orderBy('teams.name'),
        );
        $column = $makeColumn($scopedUser)->multiple()->relationship(resolveUsing: 'name');
        $queries = $connection->pretend(static fn () => $column->resolveRelatedRecords(['Editorial']));

        expect($queries)->toHaveCount(2)
            ->and($queries[1]['query'])->toContain('select distinct top 1 [teams].*', 'order by [teams].[name] asc')
            ->and($queries[1]['bindings'])->toBe(['Editorial']);
    });

    it('preserves `groupLimit()` with an offset when checking relationship coverage', function () use ($makeColumn): void {
        $user = User::factory()->create();
        Team::factory()->create(['name' => 'Editorial']);
        $firstTeam = Team::factory()->create(['name' => 'Editorial']);
        Team::factory()->create(['name' => 'Research']);
        $secondTeam = Team::factory()->create(['name' => 'Research']);
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(static function () use ($user): BelongsToMany {
            $relationship = $user->teams()->orderBy('teams.id');
            $relationship->getQuery()->groupLimit(1, 'teams.name')->offset(1);

            return $relationship;
        });
        $column = $makeColumn($scopedUser)->multiple()->relationship(resolveUsing: 'name');
        $state = $column->castState('Editorial,Research');

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->passes())->toBeTrue()
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);

        $column->saveRelationships($state);

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);
    });

    it('does not apply Query Builder `afterQuery()` callbacks to relationship coverage probes', function () use ($makeColumn): void {
        $user = User::factory()->create();
        Team::factory()->create(['name' => 'Editorial', 'slug' => 'excluded']);
        $firstTeam = Team::factory()->create(['name' => 'Editorial', 'slug' => 'editorial']);
        $secondTeam = Team::factory()->create(['name' => 'Research', 'slug' => 'research']);
        $afterQueryCalls = 0;
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(static function () use ($user, &$afterQueryCalls): BelongsToMany {
            $relationship = $user->teams()->orderBy('teams.id');
            $relationship->getQuery()->getQuery()->afterQuery(static function (SupportCollection $records) use (&$afterQueryCalls): SupportCollection {
                $afterQueryCalls++;

                return $records->reject(static fn (object $record): bool => $record->slug === 'excluded')->values();
            });

            return $relationship;
        });
        $column = $makeColumn($scopedUser)->multiple()->relationship(resolveUsing: 'name');
        $state = $column->castState('Editorial,Research');

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->passes())->toBeTrue()
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toBe([$firstTeam->getKey(), $secondTeam->getKey()])
            ->and($afterQueryCalls)->toBe(1);

        $column->saveRelationships($state);

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);
    });

    it('keeps relationship constraints when checking each lookup value across columns', function () use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'Editorial', 'slug' => 'first']);
        $secondTeam = Team::factory()->create(['name' => 'Editorial', 'slug' => 'second']);
        Team::factory()->create(['name' => 'Support', 'slug' => 'excluded']);
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(
            static fn (): BelongsToMany => $user->teams()->where('teams.name', 'Editorial'),
        );
        $column = $makeColumn($scopedUser)->multiple()->relationship(resolveUsing: ['name', 'slug']);
        $state = $column->castState('Editorial,excluded');

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->errors()->get('teams'))->toBe([__('validation.exists', ['attribute' => 'teams'])])
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);
    });

    it('retains count validation for limited relationship queries', function (string $secondName, int $offset, bool $shouldPass) use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'Editorial']);
        $secondTeam = Team::factory()->create(['name' => $secondName]);
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(static function () use ($user, $offset): BelongsToMany {
            $relationship = $user->teams()->orderBy('teams.id');
            $relationship->getQuery()->withGlobalScope('window', static fn (Builder $query): Builder => $query->limit(1)->offset($offset));

            return $relationship;
        });
        $column = $makeColumn($scopedUser)->multiple()->relationship(resolveUsing: 'name');
        $state = $column->castState("Editorial,{$secondName}");
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);
        $expectedKeys = [($offset === 0 ? $firstTeam : $secondTeam)->getKey()];

        expect($validator->passes())->toBe($shouldPass)
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toBe($expectedKeys);

        if ($shouldPass) {
            $column->saveRelationships($state);
        }

        expect($user->fresh()->teams->modelKeys())->toBe($shouldPass ? $expectedKeys : []);
    })->with([
        'limit excludes a value' => ['Research', 0, false],
        'offset excludes a value' => ['Research', 1, false],
        'limit retains a match for every value' => ['Editorial', 0, true],
        'offset retains a match for every value' => ['Editorial', 1, true],
    ]);

    it('does not treat a raw aggregate result as proof that every lookup value exists', function () use ($makeColumn): void {
        $user = User::factory()->create();
        $team = Team::factory()->create(['name' => 'Editorial']);
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(static function () use ($user): BelongsToMany {
            $relationship = $user->teams();
            $relationship->getQuery()->withGlobalScope('aggregate', static fn (Builder $query): Builder => $query->select([])->selectRaw('MAX(teams.id) as id'));

            return $relationship;
        });
        $column = $makeColumn($scopedUser)->multiple()->relationship(resolveUsing: 'name');
        $state = $column->castState('Editorial,Missing');

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->fails())->toBeTrue()
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toBe([$team->getKey()]);
    });

    it('looks up the related key when it differs from the model primary key', function () use ($makeColumn): void {
        $user = User::factory()->create();
        $team = Team::factory()->create(['slug' => 'editorial']);
        $scopedUser = Mockery::mock(User::class)->makePartial();
        $scopedUser->shouldReceive('teams')->andReturnUsing(
            static fn (): BelongsToMany => $user->belongsToMany(Team::class, 'team_user', 'user_id', 'role', 'id', 'slug'),
        );
        $column = $makeColumn($scopedUser)->multiple()->relationship();
        $state = $column->castState('editorial,editorial');

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->errors()->all())->toBe([])
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toBe([$team->getKey()]);
    });

    it('uses database equality when validating numeric primary keys', function () use ($makeColumn): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $column = $makeColumn($user)->multiple()->relationship();
        $state = $column->castState("0{$team->getKey()},0{$team->getKey()}");

        expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->errors()->all())->toBe([]);

        $column->saveRelationships($state);

        expect($user->fresh()->teams->modelKeys())->toBe([$team->getKey()]);
    });

    it('checks differently typed scalar bindings separately and caches their coverage', function (array $state, array $expectedBindings) use ($makeColumn): void {
        $user = User::factory()->create();
        Team::factory()->create(['id' => 37, 'name' => '1']);
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: 'name');
        $connection = $user->getConnection();
        $connection->flushQueryLog();
        $connection->enableQueryLog();

        try {
            $records = $column->resolveRelatedRecords($state);

            expect(Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()])->errors()->all())->toBe([])
                ->and($column->resolveRelatedRecords($state))->toBe($records)
                ->and(array_column($connection->getQueryLog(), 'bindings'))->toBe($expectedBindings);
        } finally {
            $connection->disableQueryLog();
            $connection->flushQueryLog();
        }
    })->with([
        'integer first' => [[1, '1', 1], [[1, '1', 1], [1], ['1']]],
        'string first' => [['1', 1, '1'], [['1', 1, '1'], ['1'], [1]]],
    ]);

    it('treats equivalent keys as one lookup after casting', function (bool $isNumeric) use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create();
        $secondTeam = Team::factory()->create();
        Team::factory()->create();
        $column = $makeColumn($user)->multiple()->relationship();
        $state = $isNumeric
            ? $column->integer()->castState(" 0{$firstTeam->getKey()}, {$firstTeam->getKey()}, {$secondTeam->getKey()} ")
            : [$firstTeam->getKey(), (string) $firstTeam->getKey(), $secondTeam->getKey()];
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);

        expect($validator->errors()->all())->toBe([])
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);

        $column->saveRelationships($state);

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()]);
    })->with([false, true]);

    it('does not collapse distinct numeric-looking titles when checking for missing records', function (bool $hasMissingValue) use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => '1']);
        $duplicateTeam = Team::factory()->create(['name' => '1']);
        $secondTeam = $hasMissingValue ? null : Team::factory()->create(['name' => '01']);
        $state = ['1', '1', '01'];
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: 'name');
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);
        $expectedKeys = $secondTeam ? [$firstTeam->getKey(), $duplicateTeam->getKey(), $secondTeam->getKey()] : [$firstTeam->getKey(), $duplicateTeam->getKey()];

        expect($validator->fails())->toBe($hasMissingValue)
            ->and($validator->errors()->get('teams'))->toBe($hasMissingValue ? [__('validation.exists', ['attribute' => 'teams'])] : [])
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toEqualCanonicalizing($expectedKeys);

        if (! $hasMissingValue) {
            $column->saveRelationships($state);
        }

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing($hasMissingValue ? [] : $expectedKeys);
    })->with([false, true]);

    it('preserves a custom resolver collection including its duplicate occurrences and order', function () use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create();
        $secondTeam = Team::factory()->create();
        $records = new Collection([$secondTeam, $firstTeam, $firstTeam]);
        $state = ['Research', 'Editorial', 'Editorial'];
        $calls = 0;
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: static function (array $state) use ($records, &$calls): Collection {
            $calls++;
            expect($state)->toBe(['Research', 'Editorial', 'Editorial']);

            return $records;
        });
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);

        expect($validator->errors()->all())->toBe([])
            ->and($column->resolveRelatedRecords($state))->toBe($records);

        $column->saveRelationships($state);

        expect($calls)->toBe(1)
            ->and($user->fresh()->teams->modelKeys())->toEqualCanonicalizing([$secondTeam->getKey(), $firstTeam->getKey(), $firstTeam->getKey()]);
    });

    it('preserves structured custom state for relationship resolvers', function (bool $usesObjects, bool $hasMissingValue) use ($makeColumn): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'Editorial']);
        $secondTeam = Team::factory()->create(['name' => 'Research']);
        $column = $makeColumn($user)
            ->multiple()
            ->castStateUsing(static fn (array $state): array => array_map(
                static fn (string $name): object | array => $usesObjects ? (object) ['name' => $name] : ['name' => $name],
                $state,
            ))
            ->relationship(resolveUsing: static fn (array $state): Collection => Team::query()->whereIn('name', array_column($state, 'name'))->get());
        $state = $column->castState($hasMissingValue ? 'Editorial,Missing' : 'Editorial,Research');
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);
        $expectedKeys = $hasMissingValue ? [$firstTeam->getKey()] : [$firstTeam->getKey(), $secondTeam->getKey()];

        expect($validator->fails())->toBe($hasMissingValue)
            ->and($validator->errors()->get('teams'))->toBe($hasMissingValue ? [__('validation.exists', ['attribute' => 'teams'])] : [])
            ->and($column->resolveRelatedRecords($state)->modelKeys())->toEqualCanonicalizing($expectedKeys);

        if (! $hasMissingValue) {
            $column->saveRelationships($state);
        }

        expect($user->fresh()->teams->modelKeys())->toEqualCanonicalizing($hasMissingValue ? [] : $expectedKeys);
    })->with([false, true])->with([false, true]);

    it('rejects repeated values when a custom resolver returns no records', function (bool $returnsNull) use ($makeColumn): void {
        $user = User::factory()->create();
        $records = $returnsNull ? null : new Collection;
        $calls = 0;
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: static function () use ($records, &$calls): ?Collection {
            $calls++;

            return $records;
        });
        $state = ['Missing', 'Missing'];
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);

        expect($validator->errors()->get('teams'))->toBe([__('validation.exists', ['attribute' => 'teams'])])
            ->and($column->resolveRelatedRecords($state))->toBeNull()
            ->and($calls)->toBe(1)
            ->and($user->fresh()->teams)->toBeEmpty();
    })->with([false, true]);

    it('normalizes blank inputs to an empty relationship without attaching records', function (?string $input) use ($makeColumn): void {
        $user = User::factory()->create();
        Team::factory()->create();
        $column = $makeColumn($user)->multiple()->relationship();
        $state = $column->castState($input);
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);

        expect($state)->toBe([])
            ->and($validator->errors()->all())->toBe([])
            ->and($column->resolveRelatedRecords($state))->toBeEmpty();

        $column->saveRelationships($state);

        expect($user->fresh()->teams)->toBeEmpty();
    })->with([null, '', ' , , ']);

    it('resolves relationships once per row using the current row context', function (bool $isMultiple, bool $usesCustomResolver): void {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $nameMatch = Team::factory()->create(['name' => 'shared', 'slug' => 'name-match']);
        $slugMatch = Team::factory()->create(['name' => 'Other', 'slug' => 'shared']);
        $relationshipName = $isMultiple ? 'teams' : 'team';
        $calls = 0;
        $importer = app(ImportColumnTeamImporter::class, [
            'import' => app(Import::class),
            'columnMap' => ['id' => 'id', $relationshipName => $relationshipName],
            'options' => [],
        ]);
        $column = $importer->getCachedColumns()[1];
        $column
            ->name($relationshipName)
            ->multiple($isMultiple ? ',' : null)
            ->relationship(name: $relationshipName, resolveUsing: static function (mixed $state, array $data) use ($isMultiple, $usesCustomResolver, &$calls): string | Team | Collection | null {
                $calls++;

                if (! $usesCustomResolver) {
                    return $data['lookup_column'];
                }

                $query = Team::query()->whereIn($data['lookup_column'], Arr::wrap($state));

                return $isMultiple ? $query->get() : $query->first();
            });

        $importer(['id' => $firstUser->getKey(), $relationshipName => 'shared', 'lookup_column' => 'name']);
        $importer(['id' => $secondUser->getKey(), $relationshipName => 'shared', 'lookup_column' => 'slug']);

        expect($isMultiple ? $firstUser->fresh()->teams->modelKeys() : [$firstUser->fresh()->team_id])->toBe([$nameMatch->getKey()])
            ->and($isMultiple ? $secondUser->fresh()->teams->modelKeys() : [$secondUser->fresh()->team_id])->toBe([$slugMatch->getKey()])
            ->and($calls)->toBe(2)
            ->and($importer->getCachedColumns()[1])->toBe($column);
    })->with([false, true])->with([false, true]);

    it('does not carry SQL lookup coverage into a custom collection resolver on another row', function (): void {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'A']);
        $secondTeam = Team::factory()->create(['name' => 'A']);
        $records = new Collection([$firstTeam, $secondTeam]);
        $calls = 0;
        $importer = app(ImportColumnTeamImporter::class, [
            'import' => app(Import::class),
            'columnMap' => ['id' => 'id', 'teams' => 'teams'],
            'options' => [],
        ]);
        $importer->getCachedColumns()[1]->relationship(resolveUsing: static function (array $data) use ($records, &$calls): string | Collection {
            $calls++;

            return $data['use_custom_resolver'] ? $records : 'name';
        });

        expect(fn () => $importer(['id' => $firstUser->getKey(), 'teams' => 'A,A,Missing', 'use_custom_resolver' => false]))->toThrow(ValidationException::class);

        $importer(['id' => $secondUser->getKey(), 'teams' => 'A,A,Missing', 'use_custom_resolver' => true]);

        expect($firstUser->fresh()->teams)->toBeEmpty()
            ->and($secondUser->fresh()->teams->modelKeys())->toEqualCanonicalizing([$firstTeam->getKey(), $secondTeam->getKey()])
            ->and($calls)->toBe(2);
    });

    it('refreshes relationship lookups between failed and successful rows', function (): void {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $thirdUser = User::factory()->create();
        $import = ImportColumnTeamImporter::test();

        $import->import(['id' => $firstUser->getKey(), 'teams' => 'Editorial'])->assertHasErrors(['teams']);

        $team = Team::factory()->create(['name' => 'Editorial']);
        $import->import(['id' => $secondUser->getKey(), 'teams' => 'Editorial'])->assertImported();

        $team->update(['name' => 'Research']);
        $import->import(['id' => $thirdUser->getKey(), 'teams' => 'Editorial'])->assertHasErrors(['teams']);

        expect($firstUser->fresh()->teams)->toBeEmpty()
            ->and($secondUser->fresh()->teams->modelKeys())->toBe([$team->getKey()])
            ->and($thirdUser->fresh()->teams)->toBeEmpty();
    });

    it('normalizes and imports repeated team names through the complete importer', function (bool $hasMissingValue): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'Editorial']);
        $duplicateTeam = Team::factory()->create(['name' => 'Editorial']);
        $secondTeam = Team::factory()->create(['name' => 'Research']);
        $unrelatedTeam = Team::factory()->create(['name' => 'Support']);
        $user->teams()->attach($unrelatedTeam);

        $import = ImportColumnTeamImporter::test()->import([
            'id' => $user->getKey(),
            'teams' => ' Editorial , , Editorial , Research ,' . ($hasMissingValue ? ' Missing ' : ''),
        ]);

        if ($hasMissingValue) {
            $import->assertHasErrors(['teams']);
            expect($import->errors()->get('teams'))->toBe([__('validation.exists', ['attribute' => 'teams'])]);
        } else {
            $import->assertImported();
        }

        expect($import->getRecord()->is($user))->toBeTrue()
            ->and($user->fresh()->teams->modelKeys())->toEqualCanonicalizing($hasMissingValue
                ? [$unrelatedTeam->getKey()]
                : [$unrelatedTeam->getKey(), $firstTeam->getKey(), $duplicateTeam->getKey(), $secondTeam->getKey()]);
    })->with([false, true]);
});

class ImportColumnTeamImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id'),
            ImportColumn::make('teams')->multiple()->relationship(resolveUsing: 'name'),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}
