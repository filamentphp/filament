<?php

use Filament\Commands\MakeRelationManagerCommand;
use Filament\Facades\Filament;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'Team',
        '--model-namespace' => 'Filament\\Tests\\Fixtures\\Models',
        '--view' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once __DIR__ . '/../../Fixtures/Models/Team.php';
    require_once app_path('Filament/Resources/Teams/TeamResource.php');
    require_once app_path('Filament/Resources/Teams/Pages/ListTeams.php');
    require_once app_path('Filament/Resources/Teams/Pages/CreateTeam.php');
    require_once app_path('Filament/Resources/Teams/Pages/EditTeam.php');
    require_once app_path('Filament/Resources/Teams/Pages/ViewTeam.php');
    require_once app_path('Filament/Resources/Teams/Schemas/TeamForm.php');
    require_once app_path('Filament/Resources/Teams/Schemas/TeamInfolist.php');
    require_once app_path('Filament/Resources/Teams/Tables/TeamsTable.php');
    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\Teams\\TeamResource',
        'App\\Filament\\Resources\\Users\\UserResource',
    ];

    MakeRelationManagerCommand::$shouldCheckModelsForSoftDeletes = false;
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a relation manager', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a related resource', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        '--related-resource' => 'App\\Filament\\Resources\\Teams\\TeamResource',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a form schema class', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--form-schema' => 'App\\Filament\\Resources\\Teams\\Schemas\\TeamForm',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a generated form schema and table columns', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--generate' => true,
        '--related-model' => 'Filament\\Tests\\Fixtures\\Models\\Team',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a view operation', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--view' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with an infolist schema class', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--infolist-schema' => 'App\\Filament\\Resources\\Teams\\Schemas\\TeamInfolist',
        '--view' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a table class', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--table' => 'App\\Filament\\Resources\\Teams\\Tables\\TeamsTable',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with soft deletes', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--soft-deletes' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager for a `HasMany` relationship', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--associate' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
