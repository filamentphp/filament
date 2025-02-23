<?php

use Filament\Commands\MakeResourceCommand;
use Filament\Facades\Filament;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileDoesNotExist;
use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();

    MakeResourceCommand::$shouldCheckModelsForSoftDeletes = false;
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource form', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource infolist', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Schemas/PostInfolist.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource table', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource list page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/ListPosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource create page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource edit page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource view page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/ViewPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class with embedded form', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--embed-schemas' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class with embedded infolist', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--embed-schemas' => true,
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class with embedded table', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--embed-table' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate the resource form content', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--generate' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate the resource table content', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--generate' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate the form and table content embedded in a resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--embed-schemas' => true,
        '--embed-table' => true,
        '--generate' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class with soft deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource table with soft deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource edit page with soft deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a simple resource class', function (): void {
    foreach ([
        app_path('Filament/Resources/Posts/Schemas/PostForm.php'),
        app_path('Filament/Resources/Posts/Schemas/PostInfolist.php'),
        app_path('Filament/Resources/Posts/Tables/PostsTable.php'),
    ] as $path) {
        if (! file_exists($path)) {
            continue;
        }

        unlink($path);
    }

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--simple' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileDoesNotExist(app_path('Filament/Resources/Posts/Schemas/PostForm.php'));
    assertFileDoesNotExist(app_path('Filament/Resources/Posts/Schemas/PostInfolist.php'));
    assertFileDoesNotExist(app_path('Filament/Resources/Posts/Tables/PostsTable.php'));
});

it('can generate a simple resource manage page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--simple' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/ManagePosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a simple resource class without embedded schemas and table', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--not-embedded' => true,
        '--simple' => true,
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource form in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource infolist in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Schemas/PostInfolist.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource table in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource list page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/ListPosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource create page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource edit page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource view page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/ViewPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a simple resource manage page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--simple' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/ManagePosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource class', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource class with a plural parent resource name', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource class with a parent resource name with `Resource` at the end', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'UserResource',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource form', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource infolist', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Schemas/PostInfolist.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource create page', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Pages/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource edit page', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource view page', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Pages/ViewPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a nested resource class in a nested directory', function (): void {
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

    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Blog/Posts/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
