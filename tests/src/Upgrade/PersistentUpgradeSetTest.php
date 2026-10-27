<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PersistentUpgradeSetTest extends TestCase
{
    public function test_the_complete_set_migrates_fixtures_and_remains_stable_in_fresh_processes(): void
    {
        $directory = sys_get_temp_dir() . '/filament-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);

        foreach (['app', 'config', 'routes', 'tests'] as $sourceDirectory) {
            mkdir($directory . '/' . $sourceDirectory);
        }

        $expectedFiles = [];

        try {
            foreach (glob(__DIR__ . '/Fixtures/*.php.inc') as $fixture) {
                $parts = explode("-----\n", file_get_contents($fixture), 2);
                $sourceDirectory = match (basename($fixture)) {
                    'legacy_query_builder_classes.php.inc' => 'config',
                    'deprecated_enum_constants.php.inc' => 'routes',
                    'persistent_upload_restriction.php.inc' => 'tests',
                    default => 'app',
                };
                $path = $directory . '/' . $sourceDirectory . '/' . basename($fixture, '.inc');
                file_put_contents($path, $parts[0]);
                $expectedFiles[$path] = trim($parts[1] ?? $parts[0]);
            }

            $root = dirname(__DIR__, 3);
            $configuration = $directory . '/rector.php';
            $set = var_export($root . '/packages/upgrade/src/rector.php', true);
            file_put_contents($configuration, <<<PHP
                <?php

                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([
                        __DIR__ . '/app',
                        __DIR__ . '/config',
                        __DIR__ . '/routes',
                        __DIR__ . '/tests',
                    ])
                    ->withCache(__DIR__ . '/cache')
                    ->withSets([{$set}]);
                PHP);

            $runRector = function (bool $dryRun) use ($root, $configuration): Process {
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process',
                    '--config', $configuration, '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(120);
                $process->run();

                return $process;
            };

            for ($pass = 1; $pass <= 3; $pass++) {
                $process = $runRector($pass > 1);
                $this->assertSame(0, $process->getExitCode(), "Pass {$pass}:\n" . $process->getOutput() . $process->getErrorOutput());

                foreach ($expectedFiles as $path => $expected) {
                    $this->assertSame($expected, trim(file_get_contents($path)), "Pass {$pass}: " . basename($path));
                }
            }

            // Simulate old examples being copied into an already-upgraded app.
            $reintroducedFiles = [];

            foreach (['convenience_factories', 'deprecated_action_methods', 'deprecated_lazy_loading_calls', 'deprecated_form_filter_group_methods'] as $fixture) {
                $path = $directory . '/app/' . $fixture . '.php';
                [$input] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.php.inc'), 2);
                file_put_contents($path, $input);
                $reintroducedFiles[$path] = $input;
            }

            $process = $runRector(true);
            $this->assertSame(2, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

            foreach ($reintroducedFiles as $path => $input) {
                $this->assertSame($input, file_get_contents($path));
            }

            $process = $runRector(false);
            $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

            foreach ($expectedFiles as $path => $expected) {
                $this->assertSame($expected, trim(file_get_contents($path)));
            }

            for ($pass = 1; $pass <= 2; $pass++) {
                $process = $runRector(true);
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }
}
