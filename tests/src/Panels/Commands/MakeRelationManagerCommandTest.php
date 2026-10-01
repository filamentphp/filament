<?php

use Filament\Commands\MakeRelationManagerCommand;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

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
        app()->getNamespace() . 'Filament\\Resources\\Teams\\TeamResource',
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    MakeRelationManagerCommand::$shouldCheckModelsForSoftDeletes = false;
});

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

it('can run `make:filament-relation-manager` non-interactively before the relationship exists', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'members',
        'recordTitleAttribute' => 'name',
        '--related-model' => 'Filament\\Tests\\Fixtures\\Models\\Team',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists(app_path('Filament/Resources/Users/RelationManagers/MembersRelationManager.php'));
});

it('generates executable schemas from the related model rather than the owner model', function (string $relationship, bool $isPrompted, ?string $relatedModel): void {
    $resourcesNamespace = app()->getNamespace() . 'Filament\\RelatedModelResources';

    Filament::getCurrentOrDefaultPanel()->discoverResources(
        in: app_path('Filament/RelatedModelResources'),
        for: $resourcesNamespace,
    );

    $this->artisan('make:filament-resource', [
        'model' => 'Team',
        '--model-namespace' => 'Filament\\Tests\\Fixtures\\Models',
        '--resource-namespace' => $resourcesNamespace,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/RelatedModelResources/Teams/TeamResource.php');

    MakeRelationManagerCommand::$shouldCheckModelsForSoftDeletes = true;

    $arguments = [
        'resource' => $resourcesNamespace . '\\Teams\\TeamResource',
        'relationship' => $relationship,
        'recordTitleAttribute' => 'name',
        '--resource-namespace' => $resourcesNamespace,
        '--generate' => true,
        '--view' => true,
        '--attach' => true,
        '--panel' => 'admin',
    ];

    if ($isPrompted) {
        $this->mockConsoleOutput = true;

        $this->artisan('make:filament-relation-manager', $arguments)
            ->expectsQuestion('Do you want to link this to an existing resource?', false)
            ->expectsQuestion('What is the related model?', User::class)
            ->assertSuccessful();
    } else {
        expect($this->artisan('make:filament-relation-manager', [
            ...$arguments,
            ...($relatedModel ? ['--related-model' => $relatedModel] : []),
            '--no-interaction' => true,
        ]))->toBe(0);
    }

    $basename = str($relationship)->studly() . 'RelationManager';
    $path = app_path("Filament/RelatedModelResources/Teams/RelationManagers/{$basename}.php");
    assertFileExists($path);
    expect(file_get_contents($path))
        ->toContain("TextInput::make('email')", "TextEntry::make('email')", "TextColumn::make('email')")
        ->not->toContain("::make('company_id')", '::make(null)');

    require $path;

    $relationManager = app($resourcesNamespace . "\\Teams\\RelationManagers\\{$basename}");
    $relationManager->ownerRecord = new Team;

    expect(collect($relationManager->form(Schema::make())->getComponents())->map->getName()->all())->toContain('email');
    expect(collect($relationManager->infolist(Schema::make())->getComponents())->map->getName()->all())->toContain('email');
    expect(array_keys($relationManager->table(Table::make($relationManager))->getColumns()))->toContain('email');
    expect($relationManager->getOwnerRecord())->toBeInstanceOf(Team::class);
})->with([
    'prompted model for an unknown relationship' => ['promptedMembers', true, null],
    'explicit model without interaction' => ['selectedMembers', false, User::class],
    'inferred model without interaction' => ['users', false, null],
]);

it('generates executable title-only schemas without a loadable related model', function (string $relationship, bool $isPrompted): void {
    MakeRelationManagerCommand::$shouldCheckModelsForSoftDeletes = true;

    $arguments = [
        'resource' => 'Teams',
        'relationship' => $relationship,
        'recordTitleAttribute' => 'display_name',
        '--generate' => true,
        '--view' => true,
        '--attach' => true,
        '--panel' => 'admin',
    ];

    if ($isPrompted) {
        $this->mockConsoleOutput = true;

        $this->artisan('make:filament-relation-manager', $arguments)
            ->expectsQuestion('Do you want to link this to an existing resource?', false)
            ->expectsQuestion('What is the related model?', 'App\\Models\\NotYetCreatedMember')
            ->expectsQuestion('Does the model use soft-deletes?', false)
            ->assertSuccessful();
    } else {
        expect($this->artisan('make:filament-relation-manager', [
            ...$arguments,
            '--no-interaction' => true,
        ]))->toBe(0);
    }

    $basename = str($relationship)->studly() . 'RelationManager';
    $path = app_path("Filament/Resources/Teams/RelationManagers/{$basename}.php");
    assertFileExists($path);
    expect(file_get_contents($path))->toContain("TextEntry::make('display_name')");

    require $path;

    $relationManager = app(app()->getNamespace() . "Filament\\Resources\\Teams\\RelationManagers\\{$basename}");

    expect(collect($relationManager->form(Schema::make())->getComponents())->map->getName()->all())->toBe(['display_name']);
    expect(collect($relationManager->infolist(Schema::make())->getComponents())->map->getName()->all())->toBe(['display_name']);
    expect(array_keys($relationManager->table(Table::make($relationManager))->getColumns()))->toBe(['display_name']);
})->with([
    'no model without interaction' => ['unresolvedMembers', false],
    'prompted model that does not exist yet' => ['nonexistentMembers', true],
]);

it('can generate a relation manager with a related resource', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        '--related-resource' => app()->getNamespace() . 'Filament\\Resources\\Teams\\TeamResource',
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
        '--form-schema' => app()->getNamespace() . 'Filament\\Resources\\Teams\\Schemas\\TeamForm',
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
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        '--infolist-schema' => app()->getNamespace() . 'Filament\\Resources\\Teams\\Schemas\\TeamInfolist',
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
        '--table' => app()->getNamespace() . 'Filament\\Resources\\Teams\\Tables\\TeamsTable',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a table class and attach actions', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--attach' => true,
        '--table' => app()->getNamespace() . 'Filament\\Resources\\Teams\\Tables\\TeamsTable',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with a table class and associate actions', function (): void {
    $this->artisan('make:filament-relation-manager', [
        'resource' => 'Users',
        'relationship' => 'teams',
        'recordTitleAttribute' => 'name',
        '--associate' => true,
        '--table' => app()->getNamespace() . 'Filament\\Resources\\Teams\\Tables\\TeamsTable',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/RelationManagers/TeamsRelationManager.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a relation manager with soft-deletes', function (): void {
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
