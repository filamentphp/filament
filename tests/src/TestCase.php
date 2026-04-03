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
