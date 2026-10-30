<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RemoveExportActionCsvDelimiterRectorTest extends AbstractRectorTestCase
{
    public function test_removes_only_ineffective_export_delimiters(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/export_csv_delimiter.php.inc');
    }

    public function test_preserves_evaluation_order_identity_and_nullsafe_short_circuiting(): void
    {
        $directory = sys_get_temp_dir() . '/filament-csv-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        copy(__DIR__ . '/Fixtures/export_csv_runtime.php', $directory . '/calls.php');

        try {
            for ($pass = 0; $pass < 2; $pass++) {
                $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/calls.php', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar', ...($pass ? ['--dry-run'] : [])], $root);
                $process->setTimeout(120);
                $process->mustRun();
                $runtime = new Process([PHP_BINARY, '-r', 'require $argv[1]; require $argv[2];', $root . '/vendor/autoload.php', $directory . '/calls.php'], $root);
                $runtime->mustRun();
                $this->assertSame("PASS\n", $runtime->getOutput());
                $this->assertSame('', $runtime->getErrorOutput());
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/export_csv_delimiter_config.php';
    }
}
