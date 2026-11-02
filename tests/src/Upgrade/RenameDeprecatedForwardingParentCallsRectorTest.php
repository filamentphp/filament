<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameDeprecatedForwardingParentCallsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_repairs_parent_calls_without_renaming_custom_aliases_or_their_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_parent_forwarding_methods.php.inc');
    }

    public function test_preserves_custom_parent_methods_and_unsupported_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_parent_forwarding_boundaries.php.inc');
    }

    public function test_repairs_filter_and_static_alignment_parent_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/filter_and_alignment_parent_aliases.php.inc');
    }

    public function test_retained_parent_hooks_preserve_runtime_dispatch_after_migration(): void
    {
        $directory = sys_get_temp_dir() . '/filament-parent-hooks-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);
        $root = dirname(__DIR__, 3);

        try {
            foreach (['deprecated_component_api_names_manual', 'deprecated_generator_and_dashboard_manual_boundaries'] as $fixture) {
                [$original] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.php.inc'), 2);
                file_put_contents($directory . '/app/' . $fixture . '.php', $original);
            }

            file_put_contents($directory . '/rector.php', <<<PHP
                <?php
                use Rector\Config\RectorConfig;
                return RectorConfig::configure()
                    ->withPaths([__DIR__ . '/app'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withSets(['{$root}/packages/upgrade/src/rector.php']);
                PHP);

            $probe = <<<'PHP'
                require $argv[1] . '/vendor/autoload.php';
                $container = new Illuminate\Container\Container;
                Illuminate\Container\Container::setInstance($container);
                $container->instance('config', new Illuminate\Config\Repository([
                    'filament' => ['default_filesystem_disk' => 'public'],
                    'filesystems' => ['disks' => ['local' => []]],
                ]));
                foreach (glob($argv[2] . '/app/*.php') as $file) {
                    require $file;
                }
                $exporter = new class(new Filament\Actions\Exports\Models\Export, [], []) extends Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNamesManual\DescendantExporter {
                    public int $visits = 0;
                    public static function getColumns(): array { return []; }
                    public static function getCompletedNotificationBody(Filament\Actions\Exports\Models\Export $export): string { return ''; }
                    public function getFileDisk(): string {
                        if (++$this->visits > 1) { throw new LogicException('Recursive exporter hook'); }
                        return parent::getFileDisk();
                    }
                };
                $getter = new class('App\Table', 'App\Model', null, false, false, false, false) extends Filament\Tests\Upgrade\Fixtures\GeneratorAndDashboardManualBoundaries\CustomGetter {
                    public int $visits = 0;
                    public function getTableActions(): array {
                        if (++$this->visits > 1) { throw new LogicException('Recursive generator getter'); }
                        return parent::getTableActions();
                    }
                    public function initialize(): void { $this->namespace = new Nette\PhpGenerator\PhpNamespace('App'); }
                };
                $getter->initialize();
                $output = new class('App\Table', 'App\Model', null, false, false, false, false) extends Filament\Tests\Upgrade\Fixtures\GeneratorAndDashboardManualBoundaries\InheritedParentOutput {
                    public int $recordVisits = 0;
                    public int $bulkVisits = 0;
                    public function outputTableActions(): string {
                        if (++$this->recordVisits > 1) { throw new LogicException('Recursive record output'); }
                        return parent::outputTableActions();
                    }
                    public function outputTableMethodBulkActions(): string {
                        if (++$this->bulkVisits > 1) { throw new LogicException('Recursive bulk output'); }
                        return parent::outputTableMethodBulkActions();
                    }
                    public function initialize(): void { $this->namespace = new Nette\PhpGenerator\PhpNamespace('App'); }
                };
                $output->initialize();
                echo json_encode([
                    $exporter->getFileDiskName(), $exporter->visits,
                    $getter->getTableRecordActions(), $getter->visits,
                    $output->outputTableRecordActions(), $output->recordVisits,
                    $output->outputTableBulkActions(), $output->bulkVisits,
                ], JSON_THROW_ON_ERROR);
                PHP;

            $original = new Process([PHP_BINARY, '-r', $probe, $root, $directory], $root);
            $original->mustRun();
            $this->assertSame(['local', 1, ['Filament\\Actions\\EditAction'], 1, 'EditAction::make(),', 1, 'DeleteBulkAction::make(),', 1], json_decode($original->getOutput(), associative: true, flags: JSON_THROW_ON_ERROR));

            foreach ([false, true] as $dryRun) {
                $migration = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar', ...($dryRun ? ['--dry-run'] : [])], $root);
                $migration->setTimeout(60);
                $migration->run();
                $this->assertSame(0, $migration->getExitCode(), $migration->getOutput() . $migration->getErrorOutput());
            }

            $migrated = new Process([PHP_BINARY, '-r', $probe, $root, $directory], $root);
            $migrated->mustRun();
            $this->assertSame($original->getOutput(), $migrated->getOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
