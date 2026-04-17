<?php

namespace Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\QueryBuilder\QueryBuilderServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\SpatieLaravelSettingsPluginServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Providers\AdminPanelProvider;
use Filament\Tests\Fixtures\Providers\AppAuthenticationPanelProvider;
use Filament\Tests\Fixtures\Providers\ConfigurationPanelProvider;
use Filament\Tests\Fixtures\Providers\CustomPanelProvider;
use Filament\Tests\Fixtures\Providers\DomainTenancyPanelProvider;
use Filament\Tests\Fixtures\Providers\EmailAuthenticationPanelProvider;
use Filament\Tests\Fixtures\Providers\Fixtures\Providers\SingleDomainPanel;
use Filament\Tests\Fixtures\Providers\MultiDomainPanel;
use Filament\Tests\Fixtures\Providers\RequiredMultiFactorAuthenticationPanelProvider;
use Filament\Tests\Fixtures\Providers\SlugsPanelProvider;
use Filament\Tests\Fixtures\Providers\SlugTenancyPanelProvider;
use Filament\Tests\Fixtures\Providers\TenancyPanelProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Kirschbaum\PowerJoins\PowerJoinsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
use PDOException;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;
use Spatie\MediaLibrary\Downloaders\DefaultDownloader;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Observers\MediaObserver;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithWorkbench;

    protected function getPackageProviders($app): array
    {
        $providers = [
            ActionsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            QueryBuilderServiceProvider::class,
            SchemasServiceProvider::class,
            SpatieLaravelSettingsPluginServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            AdminPanelProvider::class,
            ConfigurationPanelProvider::class,
            CustomPanelProvider::class,
            EmailAuthenticationPanelProvider::class,
            AppAuthenticationPanelProvider::class,
            RequiredMultiFactorAuthenticationPanelProvider::class,
            DomainTenancyPanelProvider::class,
            MultiDomainPanel::class,
            SingleDomainPanel::class,
            SlugsPanelProvider::class,
            SlugTenancyPanelProvider::class,
            TenancyPanelProvider::class,
            PowerJoinsServiceProvider::class,
        ];

        sort($providers);

        return $providers;
    }

    protected function defineEnvironment($app): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('media-library', [
            'disk_name' => 'public',
            'max_file_size' => 1024 * 1024 * 10,
            'queue_connection_name' => 'sync',
            'queue_name' => '',
            'queue_conversions_by_default' => false,
            'media_model' => Media::class,
            'media_observer' => MediaObserver::class,
            'use_default_collection_serialization' => false,
            'file_namer' => DefaultFileNamer::class,
            'path_generator' => DefaultPathGenerator::class,
            'url_generator' => DefaultUrlGenerator::class,
            'moves_media_on_update' => false,
            'version_urls' => true,
            'image_optimizers' => [],
            'image_generators' => [],
            'image_driver' => 'gd',
            'ffmpeg_path' => '/usr/bin/ffmpeg',
            'ffprobe_path' => '/usr/bin/ffprobe',
            'temporary_directory_path' => null,
            'jobs' => [],
            'media_downloader' => DefaultDownloader::class,
            'remote' => ['extra_headers' => []],
            'responsive_images' => [
                'use_tiny_placeholders' => true,
                'tiny_placeholder_generator' => null,
            ],
            'enable_vapor_uploads' => false,
            'default_loading_attribute_value' => null,
            'prefix' => '',
        ]);
        $app['config']->set('settings', [
            'settings' => [],
            'default_repository' => 'database',
            'repositories' => [
                'database' => [
                    'type' => DatabaseSettingsRepository::class,
                    'model' => null,
                    'table' => 'spatie_settings',
                    'connection' => null,
                ],
            ],
            'cache' => [
                'enabled' => false,
                'store' => null,
                'prefix' => null,
                'ttl' => null,
                'memo' => false,
            ],
            'auto_discover_settings' => [],
            'global_casts' => [],
            'encoder' => null,
            'decoder' => null,
        ]);
        $app['config']->set('view.paths', [
            ...$app['config']->get('view.paths'),
            __DIR__ . '/../resources/views',
        ]);

        // Paratest sets TEST_TOKEN for each worker (0, 1, 2, etc.)
        $testToken = env('TEST_TOKEN', '');
        $dbSuffix = $testToken !== '' ? "_{$testToken}" : '';
        $dbName = env('DB_DATABASE', 'testing') . $dbSuffix;

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', ':memory:'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('MYSQL_PORT', env('DB_PORT', '3306')),
            'database' => $dbName,
            'username' => env('MYSQL_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('MYSQL_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'timezone' => '+00:00',
            'options' => [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        ]);

        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_PORT', env('DB_PORT', '5432')),
            'database' => $dbName,
            'username' => env('PGSQL_USERNAME', env('DB_USERNAME', 'postgres')),
            'password' => env('PGSQL_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
            'options' => [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        ]);

        $connection = env('DB_CONNECTION', 'testing');

        // Auto-create test databases for MySQL/PostgreSQL parallel workers
        if ($dbSuffix !== '' && in_array($connection, ['mysql', 'pgsql'])) {
            $this->ensureDatabaseExists($connection, $dbName, $app['config']->get("database.connections.{$connection}"));
        }

        $app['config']->set('database.default', $connection);
        $app['config']->set('database.connections.testing', $app['config']->get('database.connections.sqlite'));
    }

    protected function ensureDatabaseExists(string $driver, string $dbName, array $config): void
    {
        try {
            if ($driver === 'mysql') {
                $pdo = new PDO(
                    "mysql:host={$config['host']};port={$config['port']}",
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
                $pdo->exec("create database if not exists `{$dbName}` character set {$config['charset']} collate {$config['collation']}");
            } elseif ($driver === 'pgsql') {
                $pdo = new PDO(
                    "pgsql:host={$config['host']};port={$config['port']};dbname=postgres",
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
                $result = $pdo->query("select 1 from pg_database where datname = '{$dbName}'");
                if ($result->fetchColumn() === false) {
                    $pdo->exec("create database \"{$dbName}\"");
                }
            }
        } catch (PDOException) {
            // Database might already exist or connection failed - let it fail later with a clearer error
        }
    }
}
