<?php

namespace Filament\Tests\Rector\Rules;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class NamingConsistencyUpgradeTest extends TestCase
{
    public function test(): void
    {
        $directory = sys_get_temp_dir() . '/filament-naming-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 4);
        [$source, $expected] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/NamingConsistencyUpgrade/names.php.inc'), 2);
        $path = $directory . '/names.php';

        try {
            file_put_contents($path, $source);

            foreach ([false, true] as $dryRun) {
                // Fresh processes load the shipped PHPStan stubs before inferring fluent return types.
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', $path,
                    '--config', $root . '/packages/upgrade/src/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root, ['TMPDIR' => $directory]);
                $process->setTimeout(120)->run();

                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
                $this->assertSame(trim($expected), trim(file_get_contents($path)));
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }
}
