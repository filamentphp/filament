<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class RemoveExportActionCsvDelimiterRectorTest extends TestCase
{
    public function test_migrates_public_calls_and_preserves_behavior_and_full_set_idempotence(): void
    {
        $directory = sys_get_temp_dir() . '/filament-csv-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        $fixture = $directory . '/calls.php';
        copy(__DIR__ . '/Fixtures/export_csv_runtime.php', $fixture);
        $configuration = $directory . '/rector.php';
        $set = var_export($root . '/packages/upgrade/src/rector.php', true);

        file_put_contents($configuration, <<<PHP
            <?php
            use Rector\Config\RectorConfig;
            return RectorConfig::configure()
                ->withPaths([__DIR__ . '/calls.php'])
                ->withCache(__DIR__ . '/cache')
                ->withSets([{$set}]);
            PHP);

        try {
            for ($pass = 0; $pass < 3; $pass++) {
                $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $configuration, '--clear-cache', '--no-progress-bar', ...($pass ? ['--dry-run'] : [])], $root);
                $process->setTimeout(120);
                $process->run();
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

                $runtime = new Process([PHP_BINARY, '-r', 'require $argv[1]; require $argv[2];', $root . '/vendor/autoload.php', $fixture], $root);
                $runtime->run();
                $this->assertSame(0, $runtime->getExitCode(), $runtime->getOutput() . $runtime->getErrorOutput() . file_get_contents($fixture));
                $this->assertSame("PASS: evaluation, effects, identity, closures, callables, nullsafe, custom dispatch, import\n", $runtime->getOutput());
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }
}
