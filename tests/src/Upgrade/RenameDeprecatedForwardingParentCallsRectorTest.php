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

    public function test_preserves_virtual_canonical_overrides_and_unsupported_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_parent_forwarding_boundaries.php.inc');
    }

    public function test_checks_configured_descendants_even_when_only_processing_the_parent_file(): void
    {
        $directory = sys_get_temp_dir() . '/filament-parent-forwarders-' . bin2hex(random_bytes(8));
        mkdir($directory);

        try {
            foreach (['parent', 'child'] as $fixture) {
                file_put_contents($directory . '/' . $fixture . '.php', file_get_contents(__DIR__ . '/Fixtures/deprecated_parent_forwarding_cross_file_' . $fixture . '.php.inc'));
            }

            file_put_contents($directory . '/rector.php', <<<'PHP'
                <?php

                use Filament\Upgrade\Rector\RenameDeprecatedForwardingMethodsRector;
                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([__DIR__ . '/parent.php', __DIR__ . '/child.php'])
                    ->withCache(__DIR__ . '/cache')
                    ->withRules([RenameDeprecatedForwardingMethodsRector::class]);
                PHP);

            $process = new Process([
                PHP_BINARY, dirname(__DIR__, 3) . '/vendor/bin/rector', 'process',
                $directory . '/parent.php', '--config', $directory . '/rector.php',
                '--dry-run', '--clear-cache', '--no-progress-bar',
            ], dirname(__DIR__, 3));
            $process->setTimeout(120);
            $process->run();

            $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
