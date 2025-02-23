<?php

use Filament\Commands\MakeRelationManagerCommand;
use Filament\Facades\Filament;
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('filament.file_generation.flags', [
        FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS,
        FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES,
        FileGenerationFlag::PARTIAL_IMPORTS,
        FileGenerationFlag::PANEL_RESOURCE_CLASSES_OUTSIDE_DIRECTORIES,
    ]);

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

    require_once __DIR__ . '/../../../Fixtures/Models/Team.php';
    require_once app_path('Filament/Resources/TeamResource.php');
    require_once app_path('Filament/Resources/TeamResource/Pages/ListTeams.php');
    require_once app_path('Filament/Resources/TeamResource/Pages/CreateTeam.php');
    require_once app_path('Filament/Resources/TeamResource/Pages/EditTeam.php');
    require_once app_path('Filament/Resources/TeamResource/Pages/ViewTeam.php');
    require_once app_path('Filament/Resources/UserResource.php');
    require_once app_path('Filament/Resources/UserResource/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/UserResource/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/UserResource/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\TeamResource',
        'App\\Filament\\Resources\\UserResource',
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

    assertFileExists($path = app_path('Filament/Resources/UserResource/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a related resource', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        '--related-resource' => 'App\\Filament\\Resources\\TeamResource',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/UserResource/RelationManagers/TeamsRelationManager.php'));
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

    assertFileExists($path = app_path('Filament/Resources/UserResource/RelationManagers/TeamsRelationManager.php'));
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

    assertFileExists($path = app_path('Filament/Resources/UserResource/RelationManagers/TeamsRelationManager.php'));
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

    assertFileExists($path = app_path('Filament/Resources/UserResource/RelationManagers/TeamsRelationManager.php'));
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

    assertFileExists($path = app_path('Filament/Resources/UserResource/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
