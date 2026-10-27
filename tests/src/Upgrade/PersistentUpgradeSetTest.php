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

        $originalFiles = [];
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
                $originalFiles[$path] = $parts[0];
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

                $runtime = new Process([PHP_BINARY, '-r', <<<'PHP'
                    require $argv[1] . '/vendor/autoload.php';
                    require $argv[2] . '/app/deprecated_number_helpers.php';
                    require $argv[2] . '/app/deprecated_exposed_attribute.php';
                    $results = \Illuminate\Support\Number::withLocale('en', fn () =>
                        \Illuminate\Support\Number::withCurrency('EUR', fn () =>
                            \Filament\Tests\Upgrade\Fixtures\NumberHelpers\results()));
                    foreach (['increment', 'inherited', 'current'] as $method) {
                        $attribute = (new ReflectionMethod(\Filament\Tests\Upgrade\Fixtures\ExposedAttribute\Counter::class, $method))->getAttributes()[0]->newInstance();
                        if (! $attribute instanceof \Filament\Support\Components\Attributes\Exposed) {
                            throw new RuntimeException('Exposure was lost.');
                        }
                    }
                    echo json_encode([
                        array_slice($results, 0, 9),
                        \Illuminate\Support\Number::withLocale('de', fn () => \Filament\Tests\Upgrade\Fixtures\NumberHelpers\results()[0]),
                        function_exists('Filament\\Support\\format_money') || function_exists('Filament\\Support\\format_number') ||
                            class_exists('Filament\\Support\\Components\\Attributes\\ExposedLivewireMethod') ||
                            interface_exists('Filament\\Schemas\\Components\\Contracts\\CanConcealComponents'),
                    ]);
                    PHP, $root, $directory], $root);
                $runtime->mustRun();
                $this->assertSame([
                    ['1,234.5', '-1,234.5', '42', '$1,234.50', '-$42.50', '£125.00', '-€1.25', '$987.65', '€12.50'],
                    '1.234,5',
                    false,
                ], json_decode($runtime->getOutput(), associative: true), "Runtime pass {$pass}");
            }

            // Reintroduce every migratable fixture, not just a subset of rules.
            $reintroducedFiles = [];

            foreach ($originalFiles as $path => $input) {
                if (trim($input) === $expectedFiles[$path]) {
                    continue;
                }

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
