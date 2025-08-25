<?php

use Filament\Commands\MakePageCommand;
use Filament\Facades\Filament;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Testing\PendingCommand;

use function PHPUnit\Framework\assertFileDoesNotExist;
use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    MakePageCommand::$shouldCheckModelsForSoftDeletes = false;
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a page class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-page', [
        'name' => 'ManageSettings',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Pages/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-page', [
        'name' => 'ManageSettings',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/pages/manage-settings.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-page', [
        'name' => 'Site/ManageSettings',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Pages/Site/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-page', [
        'name' => 'Site/ManageSettings',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/pages/site/manage-settings.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page class in a cluster', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-cluster', [
        'name' => 'Site',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Clusters/Site/SiteCluster.php');

    $this->artisan('make:filament-page', [
        'name' => 'ManageSettings',
        '--panel' => 'admin',
        '--cluster' => app()->getNamespace() . 'Filament\\Clusters\\Site\\SiteCluster',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Clusters/Site/Pages/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page view in a cluster', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-cluster', [
        'name' => 'Site',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Clusters/Site/SiteCluster.php');

    $this->artisan('make:filament-page', [
        'name' => 'ManageSettings',
        '--panel' => 'admin',
        '--cluster' => app()->getNamespace() . 'Filament\\Clusters\\Site\\SiteCluster',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/clusters/site/pages/manage-settings.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page class in a resource', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-page', [
        'name' => 'ManageUserPermissions',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'custom',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserPermissions.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page view in a resource', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-page', [
        'name' => 'ManageUserPermissions',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'custom',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/resources/users/pages/manage-user-permissions.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a create page class in a resource', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-page', [
        'name' => 'CreateUser',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'create',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/CreateUser.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileDoesNotExist(resource_path('views/filament/resources/users/pages/create-user.blade.php'));
});

it('can generate an edit page class in a resource', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-page', [
        'name' => 'EditUser',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'edit',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/EditUser.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileDoesNotExist(resource_path('views/filament/resources/users/pages/edit-user.blade.php'));
});

it('can generate a view page class in a resource', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-page', [
        'name' => 'ViewUser',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'view',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ViewUser.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileDoesNotExist(resource_path('views/filament/resources/users/pages/view-user.blade.php'));
});

$runGenerateManageRelatedRecordsPageCommand = function (TestCase $testCase): PendingCommand {
    $testCase->artisan('make:filament-resource', [
        'model' => 'Team',
        '--record-title-attribute' => 'name',
        '--view' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Should the configuration be generated from the current database columns?', false)
        ->expectsQuestion('Does the model use soft-deletes?', false);

    $testCase->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to generate a read-only view page for the resource?', false)
        ->expectsQuestion('Should the configuration be generated from the current database columns?', false)
        ->expectsQuestion('Does the model use soft-deletes?', false);

    require_once __DIR__ . '/../../Fixtures/Models/Team.php';
    require_once app_path('Filament/Resources/Teams/TeamResource.php');
    require_once app_path('Filament/Resources/Teams/Pages/ListTeams.php');
    require_once app_path('Filament/Resources/Teams/Pages/CreateTeam.php');
    require_once app_path('Filament/Resources/Teams/Pages/EditTeam.php');
    require_once app_path('Filament/Resources/Teams/Pages/ViewTeam.php');
    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Teams\\TeamResource',
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    return $testCase->artisan('make:filament-page', [
        'name' => 'ManageUserTeams',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'manage-related-records',
        '--no-interaction' => true,
    ]);
};

$generateManageRelatedRecordsPageCommandQuestions = [
    'relationship' => 'What is the relationship?',
    'hasRelatedResource' => 'Do you want to do this?',
    'relatedResource' => 'Which resource do you want to use?',
    'hasFormSchemaClass' => 'Should an existing form schema class be used?',
    'isGenerated' => 'Should the page be generated from the current database columns?',
    'relatedModel' => 'What is the related model?',
    'titleAttribute' => 'What is the title attribute for this model?',
    'formSchemaClass' => 'Which form schema class would you like to use?',
    'isGeneratedTable' => 'Should the table columns be generated from the current database columns?',
    'hasViewOperation' => 'Would you like to generate a read-only view modal for the table?',
    'hasInfolistSchemaClass' => 'Would you like to use an existing infolist schema class?',
    'infolistSchemaClass' => 'Which infolist schema class would you like to use?',
    'hasTableClass' => 'Would you like to use an existing table class?',
    'tableClass' => 'Which table class would you like to use?',
    'isSoftDeletable' => 'Does the related model use soft-deletes?',
    'relationshipType' => 'What type of relationship is this?',
];

it('can generate a manage related records page class in a resource', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['hasViewOperation'], false)
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['isSoftDeletable'], false)
        ->expectsQuestion($questions['relationshipType'], BelongsToMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileDoesNotExist(resource_path('views/filament/resources/users/pages/manage-user-teams.blade.php'));
});

it('can generate a manage related records page class in a resource with a related resource', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], true)
        ->expectsQuestion($questions['relatedResource'], app()->getNamespace() . 'Filament\\Resources\\Teams\\TeamResource')
        ->expectsQuestion($questions['relatedResource'], app()->getNamespace() . 'Filament\\Resources\\Teams\\TeamResource'); // Repeat the question as there is a bug when testing `search()` in Prompts

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with a form schema class', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], true)
        ->expectsQuestion($questions['formSchemaClass'], app()->getNamespace() . 'Filament\\Resources\\Teams\\Schemas\\TeamForm')
        ->expectsQuestion($questions['hasViewOperation'], false)
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['isGeneratedTable'], false)
        ->expectsQuestion($questions['isSoftDeletable'], false)
        ->expectsQuestion($questions['relationshipType'], BelongsToMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with a generated form schema and table columns', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], true)
        ->expectsQuestion($questions['relatedModel'], 'Filament\\Tests\\Fixtures\\Models\\Team')
        ->expectsQuestion($questions['hasViewOperation'], false)
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['isSoftDeletable'], false)
        ->expectsQuestion($questions['relationshipType'], BelongsToMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with a view operation', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['hasViewOperation'], true)
        ->expectsQuestion($questions['hasInfolistSchemaClass'], false)
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['isSoftDeletable'], false)
        ->expectsQuestion($questions['relationshipType'], BelongsToMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with an infolist schema class', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['hasViewOperation'], true)
        ->expectsQuestion($questions['hasInfolistSchemaClass'], true)
        ->expectsQuestion($questions['infolistSchemaClass'], app()->getNamespace() . 'Filament\\Resources\\Teams\\Schemas\\TeamInfolist')
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['isSoftDeletable'], false)
        ->expectsQuestion($questions['relationshipType'], BelongsToMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with a table class', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['hasViewOperation'], false)
        ->expectsQuestion($questions['hasTableClass'], true)
        ->expectsQuestion($questions['tableClass'], app()->getNamespace() . 'Filament\\Resources\\Teams\\Tables\\TeamsTable');

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with soft-deletes', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['hasViewOperation'], false)
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['isSoftDeletable'], true)
        ->expectsQuestion($questions['relationshipType'], BelongsToMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource for a `HasMany` relationship', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
    $questions = $generateManageRelatedRecordsPageCommandQuestions;

    $runGenerateManageRelatedRecordsPageCommand($this)
        ->expectsQuestion($questions['relationship'], 'teams')
        ->expectsQuestion($questions['hasRelatedResource'], false)
        ->expectsQuestion($questions['hasFormSchemaClass'], false)
        ->expectsQuestion($questions['isGenerated'], false)
        ->expectsQuestion($questions['titleAttribute'], 'name')
        ->expectsQuestion($questions['hasViewOperation'], false)
        ->expectsQuestion($questions['hasTableClass'], false)
        ->expectsQuestion($questions['isSoftDeletable'], false)
        ->expectsQuestion($questions['relationshipType'], HasMany::class);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a custom record page class in a resource', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-resource', [
        'model' => 'User',
        '--record-title-attribute' => 'name',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-page', [
        'name' => 'ManageUserPermissions',
        '--panel' => 'admin',
        '--resource' => 'Users\\UserResource',
        '--type' => 'custom',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Does this new page relate to a specific record in the resource like this?', true);

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserPermissions.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
