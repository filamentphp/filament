<?php

use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Symfony\Component\Process\Process;

use function Filament\Support\apply_search_constraint;
use function Filament\Support\encode_key_segments;
use function Filament\Support\format_money;
use function Filament\Support\format_number;
use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;
use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_pattern;
use function Filament\Support\generate_search_term_expression;
use function Filament\Support\get_color_css_variables;
use function Filament\Support\get_component_color_classes;
use function Filament\Support\get_model_label;
use function Filament\Support\is_app_url;
use function Filament\Support\is_database_driver_supported;
use function Filament\Support\is_path_within_directory;
use function Filament\Support\is_path_within_vendor_directory;
use function Filament\Support\is_slot_empty;
use function Filament\Support\locale_has_pluralization;
use function Filament\Support\original_request;
use function Filament\Support\prepare_inherited_attributes;

uses(TestCase::class);

describe('`format_money()`', function (): void {
    it('formats currency with optional division', function (): void {
        Number::withLocale('en', function (): void {
            expect(format_money(123450, 'USD', 100))->toBe('$1,234.50')
                ->and(format_money(-42.5, 'USD'))->toBe('-$42.50');
        });
    });
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

it('uses `whereLike()` without SQL expressions and with literal wildcards for unsupported database drivers', function (): void {
    $databaseConnection = new Connection(null, config: [
        'driver' => 'mongodb',
        'search_collation' => 'utf8mb4_unicode_ci',
    ]);

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
        generate_search_column_expression('profile.name', true, $databaseConnection),
        generate_search_pattern(
            generate_search_term_expression('Café!_100%[draft]\\path\\%\\_wow!!', true, $databaseConnection),
            hasLeadingWildcard: true,
            hasTrailingWildcard: true,
            databaseConnection: $databaseConnection,
        ),
        boolean: 'or',
        isInverse: true,
    );

    expect($returnedQuery)
        ->toBe($query)
        ->and($baseQuery->whereLikeArguments)
        ->toBe(['profile.name', '%Café!\\_100\\%[draft]\\path\\\\%\\\\_wow!!%', false, 'or', true]);
});

describe('`format_number()`', function (): void {
    it('uses the current number locale', function (): void {
        Number::withLocale('en', function (): void {
            expect(format_number(1234.5))->toBe('1,234.5');
        });

        Number::withLocale('de', function (): void {
            expect(format_number(-1234.5))->toBe('-1.234,5');
        });
    });
});

describe('`get_model_label()`', function (): void {
    it('converts a namespaced model name to a human-readable label', function (): void {
        expect(get_model_label(Ticket::class))->toBe('ticket')
            ->and(get_model_label('App\\Models\\PurchaseOrder'))->toBe('purchase order');
    });
});

describe('`locale_has_pluralization()`', function (): void {
    it('detects pluralization for the current application locale', function (string $locale, bool $expected): void {
        app()->setLocale($locale);

        expect(locale_has_pluralization())->toBe($expected);
    })->with(['English' => ['en', true], 'Japanese' => ['ja', false]]);
});

describe('`get_component_color_classes()`', function (): void {
    it('supports component names, instances, custom colors and blank colors', function (): void {
        expect(get_component_color_classes(BadgeComponent::class, null))->toBe([])
            ->and(get_component_color_classes(BadgeComponent::class, ''))->toBe([])
            ->and(get_component_color_classes(BadgeComponent::class, 'primary'))->toContain('fi-color-primary')
            ->and(get_component_color_classes(app(BadgeComponent::class), 'primary'))
            ->toBe(get_component_color_classes(BadgeComponent::class, 'primary'))
            ->and(get_component_color_classes(BadgeComponent::class, 'custom'))->toBe(['fi-color', 'fi-color-custom']);
    });
});

describe('`is_slot_empty()`', function (): void {
    it('distinguishes empty slots from actual content', function (?Htmlable $slot, bool $expected): void {
        expect(is_slot_empty($slot))->toBe($expected);
    })->with([
        'null' => [null, true],
        'whitespace' => [new HtmlString(" \n "), true],
        'comments' => [new ComponentSlot('<!-- comment -->'), true],
        'text' => [new ComponentSlot('0'), false],
        'HTML' => [new HtmlString('<span>Content</span>'), false],
    ]);
});

describe('`is_app_url()`', function (): void {
    it('recognizes local hosts and rejects external hosts or non-HTTP schemes', function (string $url, bool $expected): void {
        app()->instance('request', Request::create('https://app.example.test'));

        expect(is_app_url($url))->toBe($expected);
    })->with([
        'root relative' => ['/orders', true],
        'relative' => ['orders?page=2', true],
        'fragment' => ['#orders', true],
        'same host' => ['http://app.example.test:8080/orders', true],
        'same protocol-relative host' => ['//app.example.test/orders', true],
        'external' => ['https://other.example.test/orders', false],
        'external protocol-relative host' => ['//other.example.test/orders', false],
        'host suffix' => ['https://app.example.test.attacker.test', false],
        'email' => ['mailto:help@app.example.test', false],
        'script' => ['javascript:alert(1)', false],
    ]);
});

describe('`generate_icon_html()`', function (): void {
    it('returns nothing for blank icons unless an alias resolves', function (): void {
        expect(generate_icon_html(null))->toBeNull()
            ->and(generate_icon_html(''))->toBeNull();

        FilamentIcon::register(['helpers.icon' => new HtmlString('<b>Replacement</b>')]);

        expect(generate_icon_html(null, ['missing', 'helpers.icon'])->toHtml())->toContain('<b>Replacement</b>')
            ->and(generate_icon_html(new HtmlString('<b>Fallback</b>'), 'missing')->toHtml())->toContain('<b>Fallback</b>');
    });

    it('wraps custom HTML and preserves supplied attributes', function (): void {
        expect(generate_icon_html(new HtmlString('<b>Custom</b>'), attributes: new ComponentAttributeBag(['data-icon' => 'custom']), size: IconSize::Large)->toHtml())
            ->toContain('<span', '<b>Custom</b>', 'data-icon="custom"', 'fi-size-lg');
    });

    it('escapes image URLs and keeps unnamed images decorative', function (): void {
        expect(generate_icon_html('/icons/check.svg?label="check"')->toHtml())
            ->toContain('src="/icons/check.svg?label=&quot;check&quot;"', 'alt=""');
    });

    it('preserves accessible names on image icons', function (array $attributes): void {
        $html = generate_icon_html('/icons/check.svg', attributes: new ComponentAttributeBag($attributes))->toHtml();

        foreach ($attributes as $name => $value) {
            expect($html)->toContain("{$name}=\"{$value}\"");
        }

        expect($html)->not->toContain('alt=""');
    })->with([
        'alt' => [['alt' => 'Approved']],
        'label' => [['aria-label' => 'Approved']],
        'labelled by' => [['aria-labelledby' => 'approval-label']],
    ]);

    it('renders SVG strings, backed enums and size-aware icons', function (): void {
        expect(generate_icon_html('heroicon-m-check')->toHtml())->toContain('<svg')
            ->and(generate_icon_html(HelperIcon::Check)->toHtml())->toBe(generate_icon_html('heroicon-m-check')->toHtml())
            ->and(generate_icon_html(Heroicon::Check, size: IconSize::Small)->toHtml())->toContain('viewBox="0 0 16 16"')
            ->and(generate_icon_html(Heroicon::Check, size: IconSize::Large)->toHtml())->toContain('viewBox="0 0 24 24"');
    });

    it('passes SVG attributes through while omitting false and null values', function (): void {
        expect(generate_icon_html('heroicon-m-check', attributes: new ComponentAttributeBag([
            'aria-label' => 'Approved',
            'data-count' => 0,
            'data-disabled' => false,
            'data-empty' => null,
        ]))->toHtml())
            ->toContain('aria-label="Approved"', 'data-count="0"')
            ->not->toContain('data-disabled', 'data-empty');
    });
});

describe('`generate_loading_indicator_html()`', function (): void {
    it('renders with default and custom sizes and attributes', function (): void {
        expect(generate_loading_indicator_html()->toHtml())->toContain('<svg', 'fi-loading-indicator', 'fi-size-md')
            ->and(generate_loading_indicator_html(new ComponentAttributeBag(['aria-label' => 'Loading']), IconSize::Small)->toHtml())
            ->toContain('aria-label="Loading"', 'fi-size-sm');
    });
});

describe('`generate_search_term_expression()`', function (): void {
    it('applies default or explicitly configured case sensitivity', function (string $driver, ?bool $isForced, string $expected): void {
        $connection = new Connection(null, config: ['driver' => $driver]);

        expect(generate_search_term_expression('CAFÉ', $isForced, $connection))->toBe($expected);
    })->with([
        'Postgres default' => ['pgsql', null, 'café'],
        'MySQL default' => ['mysql', null, 'CAFÉ'],
        'explicit insensitive' => ['sqlite', true, 'café'],
        'explicit sensitive' => ['pgsql', false, 'CAFÉ'],
    ]);
});

describe('`apply_search_constraint()`', function (): void {
    it('matches literal wildcards and supports inverse and OR conditions', function (): void {
        Post::factory()->create(['title' => 'literal 100%_done!']);
        Post::factory()->create(['title' => 'literal 100XXdone!']);
        Post::factory()->create(['title' => 'other']);

        $matching = apply_search_constraint(Post::query(), 'title', '%100!%!_done!!');
        expect($matching->pluck('title')->all())->toBe(['literal 100%_done!']);

        $both = apply_search_constraint(Post::query()->where('title', 'other'), 'title', '%100!%!_done!!');
        expect($both->exists())->toBeFalse();

        $inverse = apply_search_constraint(Post::query(), 'title', '%100!%!_done!!', isInverse: true);
        expect($inverse->orderBy('id')->pluck('title')->all())->toBe(['literal 100XXdone!', 'other']);

        $either = apply_search_constraint(Post::query()->where('title', 'other'), new Expression('title'), '%100!%!_done!!', boolean: 'or');
        expect($either->orderBy('id')->pluck('title')->all())->toBe(['literal 100%_done!', 'other']);
    });
});

describe('`original_request()`', function (): void {
    it('returns the original request even after the current request is replaced', function (): void {
        $original = Request::create('/original');
        app()->instance('originalRequest', $original);
        app()->instance('request', Request::create('/replacement'));

        expect(original_request())->toBe($original);
    });
});

describe('`is_path_within_vendor_directory()`', function (): void {
    it('recognizes the Composer vendor directory without excluding applications installed inside it', function (): void {
        $vendorDirectory = \Filament\Support\get_composer_vendor_directory();

        expect(is_path_within_vendor_directory($vendorDirectory . '/package/Class.php', dirname($vendorDirectory)))->toBeTrue()
            ->and(is_path_within_vendor_directory($vendorDirectory . '/application/src/Class.php', $vendorDirectory . '/application'))->toBeFalse();
    });

    it('recognizes vendor segments without matching siblings', function (string $path, string $applicationDirectory, bool $expected): void {
        expect(is_path_within_vendor_directory($path, $applicationDirectory))->toBe($expected);
    })->with([
        'nested vendor' => ['/application/modules/vendor/package/Class.php', '/application', true],
        'vendor root' => ['/application/vendor', '/application', true],
        'similar segment' => ['/application/vendor-backup/Class.php', '/application', false],
        'outside application' => ['/other/vendor/Class.php', '/application', false],
        'Windows vendor' => ['C:\\APP\\modules\\VENDOR\\Class.php', 'c:/app', true],
    ]);
});

describe('`get_color_css_variables()`', function (): void {
    it('supports null, named and custom palettes', function (): void {
        expect(get_color_css_variables(null, [500]))->toBeNull()
            ->and(get_color_css_variables('primary', [400, 600]))->toBe('--color-400:var(--primary-400);--color-600:var(--primary-600)')
            ->and(get_color_css_variables([400 => '#123456', 600 => '#abcdef'], [600, 400]))->toBe('--color-600:#abcdef;--color-400:#123456')
            ->and(get_color_css_variables('primary', []))->toBe('');
    });

    it('overrides, adds and removes shades for the selected alias', function (): void {
        FilamentColor::overrideShades('helpers', [300, 500]);
        FilamentColor::addShades('helpers', [700]);
        FilamentColor::removeShades('helpers', [500]);

        expect(get_color_css_variables('primary', [100], 'helpers'))->toBe('--color-300:var(--primary-300);--color-700:var(--primary-700)')
            ->and(get_color_css_variables('primary', [100], 'other'))->toBe('--color-100:var(--primary-100)');
    });
});

describe('`encode_key_segments()`', function (): void {
    it('encodes key segments with byte lengths', function (): void {
        expect(encode_key_segments('a:b', 'c'))->toBe('3:a:b1:c')
            ->and(encode_key_segments('a', 'b:c'))->toBe('1:a3:b:c')
            ->and(encode_key_segments('', 'é|:', '42'))->toBe('0:4:é|:2:42')
            ->and(encode_key_segments())->toBe('');
    });
});

describe('`generate_search_pattern()`', function (): void {
    it('generates patterns with literal LIKE wildcard characters', function (bool $hasLeadingWildcard, bool $hasTrailingWildcard, string $expected): void {
        expect(generate_search_pattern('café!_100%[draft]\\path', $hasLeadingWildcard, $hasTrailingWildcard, Ticket::query()->getConnection()))
            ->toBe($expected);
    })->with([
        'equals' => [false, false, 'café!!!_100!%![draft]\\path'],
        'starts with' => [false, true, 'café!!!_100!%![draft]\\path%'],
        'ends with' => [true, false, '%café!!!_100!%![draft]\\path'],
        'contains' => [true, true, '%café!!!_100!%![draft]\\path%'],
    ]);
});

describe('`discover_app_classes()`', function (): void {
    it('builds a fresh index of application classes and excludes symlinked path repository classes when Composer uses a custom vendor directory', function (): void {
        $filesystem = app(Filesystem::class);
        $repositoryDirectory = dirname(__DIR__, 3);
        $temporaryRootDirectory = realpath(sys_get_temp_dir());
        $temporaryDirectory = $temporaryRootDirectory . DIRECTORY_SEPARATOR . 'filament-discover-app-classes-' . bin2hex(random_bytes(8));
        $vendorDirectory = $temporaryRootDirectory . DIRECTORY_SEPARATOR . 'filament-discover-app-classes-vendor-' . bin2hex(random_bytes(8));
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
            $filesystem->put($temporaryDirectory . DIRECTORY_SEPARATOR . 'app/ApplicationClass.php', '<?php namespace Fixture; class ApplicationClass extends \stdClass {}');
            $filesystem->put($temporaryDirectory . DIRECTORY_SEPARATOR . 'app/FreshlyIndexedClass.php', '<?php namespace Fixture; class FreshlyIndexedClass {}');
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
                $classLoader->addPsr4('Fixture\\', $applicationPathPrefix . 'app');
                $classLoader->addClassMap([
                    'Fixture\\ApplicationClass' => $applicationPathPrefix . 'app/ApplicationClass.php',
                    'Fixture\\DependencyClass' => $vendorDirectory . DIRECTORY_SEPARATOR . 'composer/../fixture/dependency/src/DependencyClass.php',
                ]);
                $classLoader->register();

                $classes = Filament\Support\discover_app_classes();
                $filteredClasses = Filament\Support\discover_app_classes(stdClass::class);

                $classLoader->addPsr4('Missing\\', $applicationPathPrefix . 'missing');
                $indexingFailed = false;
                $classesAfterFailure = Filament\Support\discover_app_classes(onIndexingFailure: static function (Throwable $exception) use (&$indexingFailed): void {
                    $indexingFailed = true;
                });

                Composer\InstalledVersions::$rootInstallPath = 'C:\\Application';
                $classLoader->addClassMap([
                    'Fixture\\WindowsApplicationClass' => 'c:/application/app/WindowsApplicationClass.php',
                    'Fixture\\WindowsSiblingClass' => 'C:/ApplicationBackup/app/WindowsSiblingClass.php',
                ]);

                echo json_encode([
                    'classes' => $classes,
                    'filteredClasses' => $filteredClasses,
                    'indexingFailed' => $indexingFailed,
                    'classesAfterFailure' => $classesAfterFailure,
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
                    'classes' => ['Fixture\\ApplicationClass', 'Fixture\\FreshlyIndexedClass'],
                    'filteredClasses' => ['Fixture\\ApplicationClass'],
                    'indexingFailed' => true,
                    'classesAfterFailure' => ['Fixture\\ApplicationClass', 'Fixture\\FreshlyIndexedClass'],
                    'windowsClasses' => ['Fixture\\WindowsApplicationClass'],
                    'vendorDirectory' => $vendorDirectory,
                ]);
        } finally {
            $filesystem->deleteDirectory($temporaryDirectory);
            $filesystem->deleteDirectory($vendorDirectory);
        }
    });
});

describe('`is_path_within_directory()`', function (): void {
    it('checks lexical directory boundaries', function (string $path, string $directory, bool $expectedResult): void {
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
        'same directory with trailing separator' => ['/app', '/app/', true],
        'Unix case mismatch' => ['/APP/Page.php', '/app', false],
    ]);
});

describe('`prepare_inherited_attributes()`', function (): void {
    it('adds camel-case aliases without replacing explicit camel-case attributes', function (): void {
        $attributes = prepare_inherited_attributes(new ComponentAttributeBag(['wire-model' => 'name', 'foo-bar' => 'original', 'fooBar' => 'explicit']));

        expect($attributes->getAttributes())->toBe(['wireModel' => 'name', 'fooBar' => 'explicit', 'wire-model' => 'name', 'foo-bar' => 'original']);
    });

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
});

describe('`generate_search_column_expression()`', function (): void {
    it('handles ordinary columns, case-sensitivity defaults and collations', function (string $driver, string $column, ?bool $isForced, ?string $collation, string $expected, bool $isExpression): void {
        $connection = new Connection(null, config: ['driver' => $driver, 'search_collation' => $collation]);
        $grammar = new MySqlGrammar($connection);
        $connection->setQueryGrammar($grammar);

        $expression = generate_search_column_expression($column, $isForced, $connection);

        expect($expression instanceof Expression)->toBe($isExpression)
            ->and($expression instanceof Expression ? $expression->getValue($grammar) : $expression)->toBe($expected);
    })->with([
        'plain column' => ['sqlite', 'title', null, null, 'title', false],
        'forced lower' => ['mysql', 'title', true, null, 'lower(title)', true],
        'Postgres default' => ['pgsql', 'title', null, null, 'lower("title"::text)', true],
        'Postgres sensitive' => ['pgsql', 'title', false, null, '"title"::text', false],
        'JSON expression default' => ['mysql', "json_extract(data, '$.name')", null, null, "lower(json_extract(data, '$.name'))", true],
        'JSON expression sensitive' => ['mysql', "json_extract(data, '$.name')", false, null, "json_extract(data, '$.name')", true],
        'collation' => ['mysql', 'title', false, 'utf8mb4_bin', 'title collate utf8mb4_bin', true],
        'MariaDB JSON' => ['mariadb', 'data->name', true, null, 'lower(json_extract(`data`, \'$."name"\'))', true],
    ]);

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
});

describe('`generate_href_html()`', function (): void {
    it('escapes URLs before rendering attributes', function (): void {
        expect(generate_href_html('/page?name="quoted"&page=2')->toHtml())->toBe('href="/page?name=&quot;quoted&quot;&amp;page=2"');
    });

    it('returns empty HTML for blank URLs', function (): void {
        expect(generate_href_html(''))->toHtml()->toBe('');
        expect(generate_href_html(null))->toHtml()->toBe('');
    });

    it('can render links with and without SPA navigation', function (): void {
        FilamentView::spa();
        expect(generate_href_html('http://localhost/page'))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate');

        FilamentView::spa(false);
        expect(generate_href_html('http://localhost/page'))
            ->toHtml()->toBe('href="http://localhost/page"');
    });

    it('can render prefetching links', function (): void {
        FilamentView::spa(true, true);
        expect(generate_href_html('http://localhost/page'))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate.hover');

        FilamentView::spa(true, false);
        expect(generate_href_html('http://localhost/page'))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate');
    });

    it('can open links in a new tab', function (): void {
        expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true))
            ->toHtml()->toBe('href="http://localhost/page" target="_blank"');

        expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: false))
            ->toHtml()->toBe('href="http://localhost/page"');
    });

    it('does not navigate external URLs in SPA mode', function (): void {
        FilamentView::spa();
        expect(generate_href_html('http://another-localhost/page'))
            ->toHtml()->toBe('href="http://another-localhost/page"');

        FilamentView::spa(false);
        expect(generate_href_html('http://another-localhost/page'))
            ->toHtml()->toBe('href="http://another-localhost/page"');
    });

    it('does not prefetch external URLs', function (): void {
        FilamentView::spa(true, true);
        expect(generate_href_html('http://another-localhost/page'))
            ->toHtml()->toBe('href="http://another-localhost/page"');
    });

    it('does not prefetch links that open in a new tab', function (): void {
        FilamentView::spa(true, true);
        expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true))
            ->toHtml()->toBe('href="http://localhost/page" target="_blank"');
    });

    it('can override SPA navigation with `$shouldOpenInSpaMode`', function (): void {
        FilamentView::spa(false);

        expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: true))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate');

        expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: false))
            ->toHtml()->toBe('href="http://localhost/page"');
    });

    it('can override SPA navigation when prefetching is enabled', function (): void {
        FilamentView::spa(true, true);

        expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: true))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate.hover');

        expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: false))
            ->toHtml()->toBe('href="http://localhost/page"');
    });

    it('does not prefetch excluded URLs', function (): void {
        FilamentView::spa(true, true);
        FilamentView::spaUrlExceptions(['*/admin/*']);

        expect(generate_href_html('http://localhost/admin/users'))
            ->toHtml()->toBe('href="http://localhost/admin/users"');

        expect(generate_href_html('http://localhost/dashboard'))
            ->toHtml()->toBe('href="http://localhost/dashboard" wire:navigate.hover');
    });

    it('uses an Alpine navigation handler when a nested click handler would conflict with `wire:navigate`', function (): void {
        FilamentView::spa(true, false);

        expect(generate_href_html('http://localhost/page', hasNestedClickEventHandler: true))
            ->toHtml()->toContain('href="http://localhost/page"', 'x-on:click=')
            ->not->toContain('wire:navigate');

        expect(generate_href_html('http://localhost/page', hasNestedClickEventHandler: false))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate');
    });

    it('preserves prefetching when a nested click handler is present', function (): void {
        FilamentView::spa(true, true);

        expect(generate_href_html('http://localhost/page', hasNestedClickEventHandler: true))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate.hover');

        expect(generate_href_html('http://localhost/page', hasNestedClickEventHandler: false))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate.hover');
    });

    it('does not add SPA navigation for nested click handlers when SPA mode is disabled', function (): void {
        FilamentView::spa(false);

        expect(generate_href_html('http://localhost/page', hasNestedClickEventHandler: true))
            ->toHtml()->toBe('href="http://localhost/page"');

        expect(generate_href_html('http://localhost/page', hasNestedClickEventHandler: false))
            ->toHtml()->toBe('href="http://localhost/page"');
    });

    it('opens links in a new tab regardless of nested click handlers', function (): void {
        FilamentView::spa(true, false);

        expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true, hasNestedClickEventHandler: true))
            ->toHtml()->toBe('href="http://localhost/page" target="_blank"');

        expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true, hasNestedClickEventHandler: false))
            ->toHtml()->toBe('href="http://localhost/page" target="_blank"');
    });

    it('honors `$shouldOpenInSpaMode` when nested click handlers are present', function (): void {
        FilamentView::spa(false);

        expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: true, hasNestedClickEventHandler: true))
            ->toHtml()->toContain('href="http://localhost/page"', 'x-on:click=')
            ->not->toContain('wire:navigate');

        expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: true, hasNestedClickEventHandler: false))
            ->toHtml()->toBe('href="http://localhost/page" wire:navigate');
    });
});

enum HelperIcon: string
{
    case Check = 'heroicon-m-check';
}
