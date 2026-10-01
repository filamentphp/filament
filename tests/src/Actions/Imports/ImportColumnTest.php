<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

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
        $secondTeam = $hasMissingValue ? null : Team::factory()->create(['name' => '01']);
        $state = ['1', '1', '01'];
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: 'name');
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);
        $expectedKeys = $secondTeam ? [$firstTeam->getKey(), $secondTeam->getKey()] : [$firstTeam->getKey()];

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
        $column = $makeColumn($user)->multiple()->relationship(resolveUsing: static fn (): ?Collection => $records);
        $state = ['Missing', 'Missing'];
        $validator = Validator::make(['teams' => $state], ['teams' => $column->getDataValidationRules()]);

        expect($validator->errors()->get('teams'))->toBe([__('validation.exists', ['attribute' => 'teams'])])
            ->and($column->resolveRelatedRecords($state))->toBeNull()
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

    it('normalizes and imports repeated team names through the complete importer', function (bool $hasMissingValue): void {
        $user = User::factory()->create();
        $firstTeam = Team::factory()->create(['name' => 'Editorial']);
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
                : [$unrelatedTeam->getKey(), $firstTeam->getKey(), $secondTeam->getKey()]);
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
