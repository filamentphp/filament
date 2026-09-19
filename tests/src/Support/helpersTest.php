<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ComponentAttributeBag;
use Symfony\Component\Process\Process;

use function Filament\get_authorization_response;
use function Filament\Support\apply_search_constraint;
use function Filament\Support\generate_search_column_expression;
use function Filament\Support\is_database_driver_supported;
use function Filament\Support\is_path_within_directory;
use function Filament\Support\prepare_inherited_attributes;

uses(TestCase::class);

it('discovers application classes and excludes symlinked path repository classes with `discover_app_classes()` when Composer uses a custom vendor directory', function (): void {
    $filesystem = app(Filesystem::class);
    $repositoryDirectory = dirname(__DIR__, 3);
    $temporaryDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'filament-discover-app-classes-' . bin2hex(random_bytes(8));
    $vendorDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'filament-discover-app-classes-vendor-' . bin2hex(random_bytes(8));
    $composerDirectory = $vendorDirectory . DIRECTORY_SEPARATOR . 'composer';
    $dependencySourceDirectory = $temporaryDirectory . DIRECTORY_SEPARATOR . 'packages/dependency';
    $linkedDependencyDirectory = $vendorDirectory . DIRECTORY_SEPARATOR . 'fixture/dependency';
    $applicationPathPrefix = $composerDirectory . DIRECTORY_SEPARATOR . '../../' . basename($temporaryDirectory) . DIRECTORY_SEPARATOR;

    try {
        $filesystem->ensureDirectoryExists($composerDirectory);
        $filesystem->ensureDirectoryExists($temporaryDirectory . DIRECTORY_SEPARATOR . 'app');
        $filesystem->ensureDirectoryExists($dependencySourceDirectory . DIRECTORY_SEPARATOR . 'src');
        $filesystem->ensureDirectoryExists(dirname($linkedDependencyDirectory));

        $filesystem->put($composerDirectory . DIRECTORY_SEPARATOR . 'InstalledVersions.php', <<<'PHP'
            <?php

            namespace Composer;

            class InstalledVersions
            {
                public static string $rootInstallPath;

                public static function getRootPackage(): array
                {
                    return ['install_path' => self::$rootInstallPath];
                }
            }
            PHP);
        $filesystem->put($vendorDirectory . DIRECTORY_SEPARATOR . 'autoload.php', <<<'PHP'
            <?php

            use Composer\Autoload\ClassLoader;

            return ClassLoader::getRegisteredLoaders()[__DIR__];
            PHP);
        $filesystem->put($temporaryDirectory . DIRECTORY_SEPARATOR . 'app/ApplicationClass.php', '<?php namespace Fixture; class ApplicationClass {}');
        $filesystem->put($dependencySourceDirectory . DIRECTORY_SEPARATOR . 'src/DependencyClass.php', '<?php namespace Fixture; class DependencyClass {}');
        $filesystem->link($dependencySourceDirectory, $linkedDependencyDirectory);

        $scriptPath = $temporaryDirectory . DIRECTORY_SEPARATOR . 'discover.php';

        $filesystem->put($scriptPath, sprintf(
            <<<'PHP'
                <?php

                require %s;
                require %s;

                $vendorDirectory = %s;
                $applicationPathPrefix = %s;
                $classLoader = new Composer\Autoload\ClassLoader($vendorDirectory);
                Composer\InstalledVersions::$rootInstallPath = $applicationPathPrefix;
                $classLoader->addClassMap([
                    'Fixture\\ApplicationClass' => $applicationPathPrefix . 'app/ApplicationClass.php',
                    'Fixture\\DependencyClass' => $vendorDirectory . DIRECTORY_SEPARATOR . 'composer/../fixture/dependency/src/DependencyClass.php',
                ]);
                $classLoader->register();

                $classes = Filament\Support\discover_app_classes();

                Composer\InstalledVersions::$rootInstallPath = 'C:\\Application';
                $classLoader->addClassMap([
                    'Fixture\\WindowsApplicationClass' => 'c:/application/app/WindowsApplicationClass.php',
                    'Fixture\\WindowsSiblingClass' => 'C:/ApplicationBackup/app/WindowsSiblingClass.php',
                ]);

                echo json_encode([
                    'classes' => $classes,
                    'windowsClasses' => Filament\Support\discover_app_classes(),
                    'vendorDirectory' => Filament\Support\get_composer_vendor_directory(),
                ], JSON_THROW_ON_ERROR);
                PHP,
            var_export($composerDirectory . DIRECTORY_SEPARATOR . 'InstalledVersions.php', return: true),
            var_export($repositoryDirectory . DIRECTORY_SEPARATOR . 'vendor/autoload.php', return: true),
            var_export($vendorDirectory, return: true),
            var_export($applicationPathPrefix, return: true),
        ));

        $process = new Process([PHP_BINARY, $scriptPath]);
        $process->mustRun();

        expect(json_decode($process->getOutput(), associative: true, flags: JSON_THROW_ON_ERROR))
            ->toBe([
                'classes' => ['Fixture\\ApplicationClass'],
                'windowsClasses' => ['Fixture\\WindowsApplicationClass'],
                'vendorDirectory' => $vendorDirectory,
            ]);
    } finally {
        $filesystem->deleteDirectory($temporaryDirectory);
        $filesystem->deleteDirectory($vendorDirectory);
    }
});

it('checks lexical directory boundaries with `is_path_within_directory()`', function (string $path, string $directory, bool $expectedResult): void {
    expect(is_path_within_directory($path, $directory))->toBe($expectedResult);
})->with([
    'Unix path' => [
        '/app/dependencies/composer/../../app/Filament/Page.php',
        '/app/dependencies/composer/../..',
        true,
    ],
    'Unix sibling path' => [
        '/app-backup/Filament/Page.php',
        '/app',
        false,
    ],
    'Windows path with different separators and casing' => [
        'C:\\APP\\dependencies\\composer\\..\\..\\app\\Filament\\Page.php',
        'c:/app/dependencies/composer/../..',
        true,
    ],
    'Windows sibling path' => [
        'C:\\ApplicationBackup\\Filament\\Page.php',
        'c:/application',
        false,
    ],
    'Windows network path with different separators and casing' => [
        '\\\\SERVER\\SHARE\\app\\Filament\\Page.php',
        '//server/share/app',
        true,
    ],
]);

it('will prepare attributes', function (): void {
    $bag = new ComponentAttributeBag([
        'style' => 'color:red',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'style' => 'color:red',
    ]);
});

it('will prepare Alpine attributes', function (): void {
    $bag = new ComponentAttributeBag([
        'x-data' => '{foo:bar}',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'x-data' => '{foo:bar}',
    ]);
});

it('will prepare data attributes', function (): void {
    $bag = new ComponentAttributeBag([
        'data-foo' => 'bar',
    ]);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe([
        'data-foo' => 'bar',
    ]);
});

it('preserves numeric keys in `prepare_inherited_attributes()`', function (array $originalAttributes, array $expectedAttributes): void {
    $bag = new ComponentAttributeBag($originalAttributes);

    $attributes = prepare_inherited_attributes($bag);

    expect($attributes->getAttributes())->toBe($expectedAttributes);
})->with([
    'integer key' => [
        [1 => 'integer'],
        ['integer', 'integer'],
    ],
    'numeric string key' => [
        ['01' => 'numeric string'],
        ['01' => 'numeric string'],
    ],
]);

it('can handle policy being an object when method does not exist', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    get_authorization_response('edit', Ticket::class);
})->throws(Exception::class, 'Strict authorization mode is enabled, but no [edit()] method was found on [Filament\Tests\Fixtures\Policies\TicketPolicy].');

it('will generate a JSON search column expression for MySQL', function (): void {
    $column = 'data->name';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('mysql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new MySqlGrammar($databaseConnection);

    $databaseConnection->shouldReceive('getQueryGrammar')->andReturn($grammar);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(json_extract(`data`, '$.\"name\"'))");
});

it('will generate a JSON search column expression for Postgres', function (): void {
    $column = 'data->name';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->>'name'::text)");
});

it('will generate a nested JSON search column expression for Postgres', function (): void {
    $column = 'data->name->value->en';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->'name'->'value'->>'en'::text)");
});

it('will generate a column expression for Postgres with colons in the table name', function (string $column, string $expectedExpression): void {
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getTablePrefix')->andReturn('');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $actualExpression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($actualExpression->getValue($grammar))
        ->toBe($expectedExpression);
})
    ->with([
        ['blog:posts.title', 'lower("blog:posts"."title"::text)'],
        ['blog:posts:comments.author.name', 'lower("blog:posts:comments"."author"."name"::text)'],
    ]);

it('will generate a search column expression for Postgres with a table prefix', function (string $column, string $expectedExpression): void {
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getTablePrefix')->andReturn('app_');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $actualExpression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($actualExpression->getValue($grammar))
        ->toBe($expectedExpression);
})
    ->with([
        ['posts.title', 'lower("app_posts"."title"::text)'],
        ['posts.data->name', "lower(\"app_posts\".\"data\"->>'name'::text)"],
    ]);

it('will generate a JSON search column expression for Postgres with explicit ->> operator', function (): void {
    $column = 'data->>name';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->>'name'::text)");
});

it('will generate a nested JSON search column expression for Postgres with explicit ->> operator on the last segment', function (): void {
    $column = 'data->name->>ar';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"data\"->'name'->>'ar'::text)");
});

it('will generate a JSON search column expression for Postgres with explicit ->> operator and simple key', function (): void {
    $column = 'name->>\'en\'';
    $isSearchForcedCaseInsensitive = true;

    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getConfig')->with('search_collation')->andReturn(null);

    $grammar = new PostgresGrammar($databaseConnection);

    $expression = generate_search_column_expression($column, $isSearchForcedCaseInsensitive, $databaseConnection);

    expect($expression->getValue($grammar))
        ->toBe("lower(\"name\"->>'en'::text)");
});

it('uses Filament search expressions for recognized database drivers', function (): void {
    $query = Ticket::query();

    $expectedSearchColumnExpression = match ($query->getConnection()->getDriverName()) {
        'pgsql' => 'lower("name"::text)',
        default => 'lower(name)',
    };

    $returnedQuery = apply_search_constraint(
        $query,
        'name',
        '%TeSt%',
        isSearchForcedCaseInsensitive: true,
        boolean: 'or',
        isInverse: true,
    );

    $where = $query->getQuery()->wheres[0];

    expect($returnedQuery)
        ->toBe($query)
        ->and($where['type'])
        ->toBe('Basic')
        ->and($where['column']->getValue($query->getQuery()->getGrammar()))
        ->toBe($expectedSearchColumnExpression)
        ->and($where['operator'])
        ->toBe('like')
        ->and($where['value'])
        ->toBe('%test%')
        ->and($where['boolean'])
        ->toBe('or not');
});

it('recognizes supported drivers with `is_database_driver_supported()`', function (string $driver, bool $isSupported): void {
    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->once()->andReturn($driver);

    expect(is_database_driver_supported($databaseConnection))->toBe($isSupported);
})->with([
    'MariaDB' => ['mariadb', true],
    'MySQL' => ['mysql', true],
    'PostgreSQL' => ['pgsql', true],
    'SQLite' => ['sqlite', true],
    'SQL Server' => ['sqlsrv', true],
    'MongoDB' => ['mongodb', false],
]);

it('can use `apply_search_constraint()` without applying a search collation', function (): void {
    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->andReturn('pgsql');
    $databaseConnection->shouldReceive('getTablePrefix')->andReturn('');
    $databaseConnection->shouldNotReceive('getConfig');

    $grammar = new PostgresGrammar($databaseConnection);
    $baseQuery = new QueryBuilder($databaseConnection, $grammar, new Processor);
    $query = (new EloquentBuilder($baseQuery))->setModel(new Ticket);

    apply_search_constraint(
        $query,
        'tickets.Name',
        '%TeSt%',
        shouldApplySearchCollation: false,
    );

    $where = $query->getQuery()->wheres[0];

    expect($where['column']->getValue($grammar))
        ->toBe('lower("tickets"."Name"::text)')
        ->and($where['value'])
        ->toBe('%test%');
});

it('uses `whereLike()` for unrecognized database drivers', function (): void {
    $databaseConnection = Mockery::mock(Connection::class);
    $databaseConnection->shouldReceive('getDriverName')->once()->andReturn('mongodb');

    $baseQuery = new class($databaseConnection, new Grammar($databaseConnection), new Processor) extends QueryBuilder
    {
        /** @var array<mixed> */
        public array $whereLikeArguments = [];

        public function whereLike($column, $value, $caseSensitive = false, $boolean = 'and', $not = false)
        {
            $this->whereLikeArguments = [$column, $value, $caseSensitive, $boolean, $not];

            return $this;
        }
    };

    $query = (new EloquentBuilder($baseQuery))->setModel(new Ticket);

    $returnedQuery = apply_search_constraint(
        $query,
        'profile.name',
        '%Te_st%',
        boolean: 'or',
        isInverse: true,
    );

    expect($returnedQuery)
        ->toBe($query)
        ->and($baseQuery->whereLikeArguments)
        ->toBe(['profile.name', '%Te_st%', false, 'or', true]);
});
