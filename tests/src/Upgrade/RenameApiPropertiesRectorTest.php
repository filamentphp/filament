<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameApiPropertiesRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_non_colliding_declarations_and_fetches_together(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/api_properties.php.inc');
    }

    #[DataProvider('collisionFixtures')]
    public function test_property_collisions_preserve_runtime_values_and_valid_promoted_declarations(string $fixture, string $expected): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/ApiProperties/' . $fixture);

        $directory = sys_get_temp_dir() . '/filament-property-collisions-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        $path = $directory . '/properties.php';
        file_put_contents($path, explode("\n-----\n", file_get_contents(__DIR__ . '/Fixtures/ApiProperties/' . $fixture), 2)[0]);

        try {
            $configurationPath = $directory . '/rector.php';
            file_put_contents($configurationPath, sprintf(<<<'PHP'
                <?php

                use Rector\Config\RectorConfig;

                return static function (RectorConfig $configuration): void {
                    (require %s)($configuration);
                    $configuration->cacheDirectory(__DIR__ . '/cache');
                    $configuration->disableParallel();
                };
                PHP, var_export($this->provideConfigFilePath(), true)));

            $command = [PHP_BINARY, '-r', 'require $argv[1]; require $argv[2]; echo json_encode(Filament\\Tests\\Upgrade\\Fixtures\\ApiPropertyCollisions\\results(), JSON_THROW_ON_ERROR);', $root . '/vendor/autoload.php', $path];
            $before = new Process($command, $root);
            $before->mustRun();
            $this->assertSame($expected, $before->getOutput());

            foreach ([false, true] as $dryRun) {
                $migration = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', $path, '--config', $configurationPath, '--clear-cache', '--no-progress-bar', ...($dryRun ? ['--dry-run'] : [])], $root);
                $migration->setTimeout(120)->mustRun();
            }

            (new Process([PHP_BINARY, '-l', $path], $root))->mustRun();
            $after = new Process($command, $root);
            $after->mustRun();
            $this->assertSame($before->getOutput(), $after->getOutput());
            $this->assertSame('', $after->getErrorOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function collisionFixtures(): array
    {
        return [
            'property collisions' => ['collisions.php.inc', '[[true,true],[true,true],[true,null],[true,true],[true,true],[true,true],true,[10,7]]'],
            'promoted parameter with destination local or non-promoted sibling collision' => ['promoted_local.php.inc', '[10,7,99,true,false]'],
            'promoted parameter with destination parameter' => ['promoted_parameter.php.inc', '[10,7,99]'],
            'independent non-promoted property' => ['non_promoted.php.inc', '[true,true]'],
        ];
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
