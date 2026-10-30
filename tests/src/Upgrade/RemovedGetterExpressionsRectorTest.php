<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RemovedGetterExpressionsRectorTest extends AbstractRectorTestCase
{
    public function test_replaces_typed_getters_and_leaves_unusual_calls_for_manual_migration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/removed_getters.php.inc');
    }

    public function test_preserves_associative_and_empty_examples_and_receiver_evaluation(): void
    {
        $directory = sys_get_temp_dir() . '/filament-getters-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        copy(__DIR__ . '/Fixtures/import_example_runtime.php', $directory . '/calls.php');

        try {
            $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/calls.php', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar'], $root);
            $process->setTimeout(120);
            $process->mustRun();
            $runtime = new Process([PHP_BINARY, '-r', 'require $argv[1]; require $argv[2];', $root . '/vendor/autoload.php', $directory . '/calls.php'], $root);
            $runtime->mustRun();
            $this->assertSame("PASS\n", $runtime->getOutput());
            $this->assertSame('', $runtime->getErrorOutput());

            $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/calls.php', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar', '--dry-run'], $root);
            $process->setTimeout(120);
            $process->mustRun();
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/removed_getters_config.php';
    }
}
