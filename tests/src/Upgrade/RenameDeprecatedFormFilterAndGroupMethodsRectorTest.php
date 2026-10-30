<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameDeprecatedFormFilterAndGroupMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_calls_on_known_receivers_with_legacy_methods_absent(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_form_filter_group_methods.php.inc');
    }

    public function test_preserves_unrelated_receivers_and_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_form_filter_group_manual_boundaries.php.inc');
    }

    public function test_keeps_migrated_calls_stable_in_a_fresh_complete_set_process(): void
    {
        $directory = sys_get_temp_dir() . '/filament-aliases-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $parts = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/deprecated_form_filter_group_manual_boundaries.php.inc'), 2);
        $fixture = $parts[1] ?? $parts[0];
        file_put_contents($directory . '/app.php', $fixture);

        try {
            $configuration = $directory . '/rector.php';
            $set = var_export($this->provideConfigFilePath(), true);
            file_put_contents($configuration, <<<PHP
                <?php

                return \Rector\Config\RectorConfig::configure()
                    ->withCache(__DIR__ . '/cache')
                    ->withSets([{$set}]);
                PHP);

            $process = new Process([
                PHP_BINARY, dirname(__DIR__, 3) . '/vendor/bin/rector', 'process', $directory . '/app.php',
                '--config', $configuration, '--clear-cache', '--no-progress-bar', '--dry-run',
            ], dirname(__DIR__, 3));
            $process->setTimeout(120);
            $process->run();

            $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            $this->assertSame($fixture, file_get_contents($directory . '/app.php'));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
