<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class ReplaceDeprecatedBehavioralConfigurationRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('fixtures')]
    public function test_migrates_configuration_and_preserves_manual_boundaries(string $fixture): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
    }

    public static function fixtures(): array
    {
        return [['behavioral_configuration'], ['behavioral_configuration_manual'], ['behavioral_configuration_expressions']];
    }

    public function test_migrates_stored_computed_and_nullsafe_inset_receivers(): void
    {
        $directory = sys_get_temp_dir() . '/filament-inset-' . bin2hex(random_bytes(8));
        mkdir($directory);
        [$source, $expected] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/behavioral_configuration_inset.php.inc'), 2);
        $root = dirname(__DIR__, 3);
        $set = var_export($this->provideConfigFilePath(), true);

        try {
            file_put_contents($directory . '/components.php', $source);
            file_put_contents($directory . '/rector.php', <<<PHP
                <?php

                return \Rector\Config\RectorConfig::configure()
                    ->withPaths([__DIR__ . '/components.php'])
                    ->withCache(__DIR__ . '/cache')
                    ->withSets([{$set}]);
                PHP);

            foreach ([false, true] as $dryRun) {
                // Fresh CLI processes avoid PHPStan's fixture-local negative cache
                // and prove the generated dispatch fallback is stable on pass two.
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process',
                    '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(120);
                $process->run();
                $this->assertTrue($process->isSuccessful(), $process->getOutput() . $process->getErrorOutput());
                $this->assertSame(trim($expected), trim(file_get_contents($directory . '/components.php')));
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
