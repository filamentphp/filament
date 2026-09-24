<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

uses(TestCase::class);

class ImportColumnTestUser extends User
{
    public function constrainedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id')
            ->where('teams.name', 'Allowed');
    }

    public function constrainedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
            ->where('teams.name', 'Allowed');
    }
}

class ImportColumnTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }

    public function record(Model $record): static
    {
        $this->record = $record;

        return $this;
    }
}

it('does not allow columns passed to `relationship()` to bypass `BelongsTo` constraints', function (): void {
    Team::factory()->create(['name' => 'Allowed']);
    $excludedTeam = Team::factory()->create(['name' => 'Excluded']);

    $importer = (new ImportColumnTestImporter(new Import, [], []))
        ->record(new ImportColumnTestUser);

    $column = ImportColumn::make('constrainedTeam')
        ->relationship(resolveUsing: ['teams.id', 'teams.name'])
        ->importer($importer);

    expect($column->resolveRelatedRecord($excludedTeam->name))->toBeNull();
});

it('does not allow columns passed to `relationship()` to bypass `BelongsToMany` constraints', function (): void {
    Team::factory()->create(['name' => 'Allowed']);
    $excludedTeam = Team::factory()->create(['name' => 'Excluded']);

    $importer = (new ImportColumnTestImporter(new Import, [], []))
        ->record(new ImportColumnTestUser);

    $column = ImportColumn::make('constrainedTeams')
        ->relationship(resolveUsing: ['teams.id', 'teams.name'])
        ->multiple()
        ->importer($importer);

    expect($column->resolveRelatedRecords([$excludedTeam->name]))->toBeEmpty();
});
