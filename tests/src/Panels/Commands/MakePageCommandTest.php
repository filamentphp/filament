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
        '--cluster' => 'App\\Filament\\Clusters\\Site\\SiteCluster',
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
        '--cluster' => 'App\\Filament\\Clusters\\Site\\SiteCluster',
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
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\Users\\UserResource',
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
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\Users\\UserResource',
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
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\Users\\UserResource',
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
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\Users\\UserResource',
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
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Resources/Users/UserResource.php');
    require_once app_path('Filament/Resources/Users/Pages/ListUsers.php');
    require_once app_path('Filament/Resources/Users/Pages/CreateUser.php');
    require_once app_path('Filament/Resources/Users/Pages/EditUser.php');

    invade(Filament::getCurrentOrDefaultPanel())->resources = [
        ...invade(Filament::getCurrentOrDefaultPanel())->resources,
        'App\\Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-page', [
        'name' => 'EditUser',
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
        '--view' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to generate the form schema and table columns based on the attributes of the model?', false)
        ->expectsQuestion('Does the model use soft deletes?', false);

    $testCase->artisan('make:filament-resource', [
        'model' => 'User',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to generate an infolist and view page for the resource?', false)
        ->expectsQuestion('Would you like to generate the form schema and table columns based on the attributes of the model?', false)
        ->expectsQuestion('Does the model use soft deletes?', false);

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
        'App\\Filament\\Resources\\Teams\\TeamResource',
        'App\\Filament\\Resources\\Users\\UserResource',
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
    'hasRelatedResource' => 'Do you want each table row to link to a resource instead of opening a modal? Filament will also inherit the resource\'s configuration.',
    'relatedResource' => 'Which resource do you want to use?',
    'hasFormSchemaClass' => 'Would you like to use an existing form schema class instead of defining the form on this page?',
    'isGenerated' => 'Would you like to generate the page based on the attributes of the model?',
    'relatedModel' => 'Filament couldn\'t automatically find the related model for the [teams] relationship. What is the fully qualified class name of the related model?',
    'titleAttribute' => 'What is the title attribute? This is the attribute that will be used to uniquely identify each record in the table.',
    'formSchemaClass' => 'Which form schema class would you like to use? Please provide the fully qualified class name.',
    'isGeneratedTable' => 'Would you like to generate the table columns based on the attributes of the model?',
    'hasViewOperation' => 'Would you like to generate an infolist and view modal for the table?',
    'hasInfolistSchemaClass' => 'Would you like to use an existing infolist schema class instead of defining the infolist on this page?',
    'infolistSchemaClass' => 'Which infolist schema class would you like to use? Please provide the fully qualified class name.',
    'hasTableClass' => 'Would you like to use an existing table class instead of defining the table on this page?',
    'tableClass' => 'Which table class would you like to use? Please provide the fully qualified class name.',
    'isSoftDeletable' => 'Does the related model use soft deletes?',
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
        ->expectsQuestion($questions['relatedResource'], 'App\\Filament\\Resources\\Teams\\TeamResource')
        ->expectsQuestion($questions['relatedResource'], 'App\\Filament\\Resources\\Teams\\TeamResource'); // Repeat the question as there is a bug when testing `search()` in Prompts

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
        ->expectsQuestion($questions['formSchemaClass'], 'App\\Filament\\Resources\\Teams\\Schemas\\TeamForm')
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
        ->expectsQuestion($questions['infolistSchemaClass'], 'App\\Filament\\Resources\\Teams\\Schemas\\TeamInfolist')
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
        ->expectsQuestion($questions['tableClass'], 'App\\Filament\\Resources\\Teams\\Tables\\TeamsTable');

    assertFileExists($path = app_path('Filament/Resources/Users/Pages/ManageUserTeams.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a manage related records page class in a resource with soft deletes', function () use ($runGenerateManageRelatedRecordsPageCommand, $generateManageRelatedRecordsPageCommandQuestions): void {
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
