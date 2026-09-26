<?php

use Composer\Autoload\ClassLoader;
use Filament\Commands\MakeResourceCommand;
use Filament\Facades\Filament;
use Filament\Tests\TestCase;

use function Filament\Support\get_composer_vendor_directory;
use function PHPUnit\Framework\assertFileDoesNotExist;
use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();

    MakeResourceCommand::$shouldCheckModelsForSoftDeletes = false;
});

it('warns and continues when refreshing the application class index fails', function (): void {
    $this->mockConsoleOutput = true;

    $vendorDirectory = get_composer_vendor_directory();
    $originalClassLoader = ClassLoader::getRegisteredLoaders()[$vendorDirectory];
    $failingClassLoader = new class($vendorDirectory) extends ClassLoader
    {
        public function getPrefixesPsr4()
        {
            throw new RuntimeException('Unable to read PSR-4 prefixes.');
        }
    };

    foreach ($originalClassLoader->getPrefixesPsr4() as $namespace => $directories) {
        $failingClassLoader->addPsr4($namespace, $directories);
    }

    $failingClassLoader->addPsr4('', $originalClassLoader->getFallbackDirsPsr4());

    foreach ($originalClassLoader->getPrefixes() as $namespace => $directories) {
        $failingClassLoader->add($namespace, $directories);
    }

    $failingClassLoader->add('', $originalClassLoader->getFallbackDirs());
    $failingClassLoader->addClassMap($originalClassLoader->getClassMap());
    $failingClassLoader->register();

    try {
        $this->artisan('make:filament-resource', [
            '--panel' => 'admin',
            '--record-title-attribute' => 'name',
        ])
            ->expectsOutputToContain('Unable to refresh the application class index. Model suggestions may be incomplete.')
            ->expectsQuestion('What is the model?', 'Filament\Tests\Fixtures\Models\User')
            ->expectsQuestion('Would you like to generate a read-only view page for the resource?', false)
            ->expectsQuestion('Should the configuration be generated from the current database columns?', false)
            ->expectsQuestion('Does the model use soft-deletes?', false)
            ->assertSuccessful();
    } finally {
        $this->mockConsoleOutput = false;
        $failingClassLoader->unregister();
        $originalClassLoader->register();
    }
});

it('can generate a resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource class with a record title attribute', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--record-title-attribute' => 'title',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource form', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource table', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource list page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/ListPosts.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource create page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/CreatePost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource edit page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate the resource infolist content', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--generate' => true,
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Schemas/PostInfolist.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate the form, infolist, and table content embedded in a resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--embed-schemas' => true,
        '--embed-table' => true,
        '--generate' => true,
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('does not add binary columns to generated resource schemas', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--generate' => true,
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    // The `location` column is `binary`, which cannot be serialized to the browser, so it should be skipped.
    expect(file_get_contents(app_path('Filament/Resources/Posts/Schemas/PostForm.php')))
        ->not->toContain("'location'");
    expect(file_get_contents(app_path('Filament/Resources/Posts/Schemas/PostInfolist.php')))
        ->not->toContain("'location'");
    expect(file_get_contents(app_path('Filament/Resources/Posts/Tables/PostsTable.php')))
        ->not->toContain("'location'");
});

it('can generate a resource class with soft-deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource table with soft-deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource edit page with soft-deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource class in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource form in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource table in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Tables/PostsTable.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource list page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/ListPosts.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource create page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/CreatePost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a resource edit page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'UserResource',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Schemas/PostForm.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Pages/CreatePost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Posts/Pages/EditPost.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
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
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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
        app()->getNamespace() . 'Filament\\Resources\\Users\\UserResource',
    ];

    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--nested' => 'Users',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Users/Resources/Blog/Posts/PostResource.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});
