<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenamePageConfigurationRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_sticky_configuration_without_changing_width_apis(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/configuration.php.inc');
    }

    public function test_migrates_trait_configuration_and_inherited_references(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/sticky_traits.php.inc');
    }

    public function test_preserves_shared_unrelated_traits_and_conflicting_properties(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/sticky_trait_boundaries.php.inc');
    }

    public function test_migrates_cross_file_sticky_traits_without_changing_configuration(): void
    {
        $directory = sys_get_temp_dir() . '/filament-sticky-traits-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);
        $root = dirname(__DIR__, 3);

        try {
            foreach (['Settings.php', 'Pages.php'] as $filename) {
                copy(__DIR__ . '/Fixtures/PageConfiguration/CrossFile/' . $filename, $directory . '/app/' . $filename);
            }

            file_put_contents($directory . '/verify.php', <<<'PHP'
                <?php

                require $argv[1] . '/vendor/autoload.php';

                if ($argv[2] === 'legacy') {
                    // Reproduce the former backing property, getter and setter; the migrated run loads the real `BasePage`.
                    eval('namespace Filament\Pages; abstract class BasePage {
                        public static bool $formActionsAreSticky = false;
                        public function areFormActionsSticky(): bool { return static::$formActionsAreSticky; }
                        public static function stickyFormActions(bool $condition = true): void { static::$formActionsAreSticky = $condition; }
                    }');
                }

                require __DIR__ . '/app/Settings.php';
                require __DIR__ . '/app/Pages.php';

                $page = new Filament\Tests\Upgrade\Fixtures\StickyCrossFile\ChildPage;
                $read = fn (): array => [$page->configuredSticky(), $page->areFormActionsSticky()];
                $states = [$read()];
                $page::disable();
                $states[] = $read();
                $page->enable();
                $states[] = $read();
                $page->configure(false);
                $states[] = $read();
                Filament\Tests\Upgrade\Fixtures\StickyCrossFile\configure($page);
                $states[] = $read();
                $page::stickyFormActions(false);
                $states[] = $read();
                $page::stickyFormActions();
                $states[] = $read();
                echo json_encode($states, JSON_THROW_ON_ERROR);
                PHP);

            $legacy = new Process([PHP_BINARY, $directory . '/verify.php', $root, 'legacy'], $root);
            $legacy->mustRun();
            $this->assertSame('[[false,false],[false,false],[true,true],[false,false],[true,true],[false,false],[true,true]]', $legacy->getOutput());

            $command = [PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/app', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar'];
            foreach ([false, true] as $dryRun) {
                $process = new Process([...$command, ...($dryRun ? ['--dry-run'] : [])], $root);
                $process->setTimeout(120)->mustRun();
            }

            foreach (['Settings.php', 'Pages.php'] as $filename) {
                $this->assertStringNotContainsString('formActionsAreSticky', file_get_contents($directory . '/app/' . $filename));
            }

            $migrated = new Process([PHP_BINARY, $directory . '/verify.php', $root, 'migrated'], $root);
            $migrated->mustRun();
            $this->assertSame($legacy->getOutput(), $migrated->getOutput());
            $this->assertSame('', $migrated->getErrorOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
