<?php

namespace Filament\Tests\Upgrade;

use Filament\Forms\Components\TextInput;
use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameSchemaComponentHooksRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_component_hooks_and_their_calls_together(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_component_hooks.php.inc');
    }

    public function test_migrates_configuration_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_configuration_calls.php.inc');
    }

    public function test_preserves_unrelated_configuration_contracts(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_configuration_boundaries.php.inc');
    }

    public function test_combines_legacy_operator_class_and_hook_renames(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_legacy_operator_hook.php.inc');
    }

    public function test_migrates_only_supported_schema_lookup_arguments(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_lookup_arguments.php.inc');
    }

    public function test_migrates_parent_and_child_hooks_in_a_fresh_process(): void
    {
        $directory = sys_get_temp_dir() . '/filament-schema-hooks-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);

        try {
            foreach ([
                'parent.php' => __DIR__ . '/Fixtures/SchemaHooks/ParentExporter.php',
                'child.php' => __DIR__ . '/Fixtures/schema_cross_file_hook.php.inc',
            ] as $filename => $fixture) {
                $source = explode("-----\n", file_get_contents($fixture), 2)[0];
                $source = str_replace('Filament\\Tests\\Upgrade\\Fixtures\\', 'Filament\\Tests\\Upgrade\\MigratedSchemaHooks\\', $source);
                file_put_contents($directory . '/app/' . $filename, $source);
            }

            $root = dirname(__DIR__, 3);
            file_put_contents($directory . '/rector.php', <<<'PHP'
                <?php

                use Filament\Upgrade\Rector\RenameSchemaComponentHooksRector;
                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([__DIR__ . '/app'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withRules([RenameSchemaComponentHooksRector::class]);
                PHP);

            foreach ([false, true] as $dryRun) {
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process',
                    '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(60);
                $process->run();

                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            }

            require $directory . '/app/parent.php';
            require $directory . '/app/child.php';

            $components = MigratedSchemaHooks\SchemaCrossFileHook\ChildExporter::getOptionsSchemaComponents();
            $this->assertSame(['parent_option', 'child_option'], array_map(static fn (TextInput $component): string => $component->getName(), $components));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
