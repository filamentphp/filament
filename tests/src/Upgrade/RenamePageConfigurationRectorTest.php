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

    public function test_migrates_sticky_and_width_configuration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/configuration.php.inc');
    }

    public function test_migrates_inherited_and_trait_defined_width_configuration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/page_width.php.inc');
    }

    public function test_preserves_conflicting_width_configuration_for_manual_reconciliation(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/width_conflicts.php.inc');
    }

    public function test_migrates_trait_configuration_and_inherited_references(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/sticky_traits.php.inc');
    }

    public function test_preserves_shared_unrelated_traits_and_conflicting_properties(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/PageConfiguration/sticky_trait_boundaries.php.inc');
    }

    public function test_width_migration_preserves_configured_values_in_fresh_processes(): void
    {
        $directory = sys_get_temp_dir() . '/filament-page-width-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);
        $root = dirname(__DIR__, 3);
        [$source] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/page_width.php.inc'), 2);

        try {
            file_put_contents($directory . '/app/PageWidth.php', $source);
            file_put_contents($directory . '/verify.php', <<<'PHP'
                <?php

                require $argv[1] . '/vendor/autoload.php';

                if ($argv[2] === 'legacy') {
                    // Reproduce the released width storage and getter on both page types.
                    eval('namespace Filament\Pages; abstract class SimplePage extends BasePage {
                        protected \Filament\Support\Enums\Width | string | null $maxWidth = null;
                        public function getMaxWidth(): \Filament\Support\Enums\Width | string | null { return $this->maxWidth; }
                    }');
                    eval('namespace Filament\Auth\Pages; class EditProfile extends \Filament\Pages\Page {
                        protected \Filament\Support\Enums\Width | string | null $maxWidth = null;
                        public function getMaxWidth(): \Filament\Support\Enums\Width | string | null { return $this->maxWidth; }
                    }');
                }

                require __DIR__ . '/app/PageWidth.php';

                $getter = $argv[2] === 'legacy' ? 'getMaxWidth' : 'getMaxContentWidth';
                $page = new Filament\Tests\Upgrade\Fixtures\PageWidth\ChildLogin;
                $states = [Filament\Tests\Upgrade\Fixtures\PageWidth\width($page)];
                foreach ([\Filament\Support\Enums\Width::Small, 'custom-width', '', null] as $width) {
                    $page->configureWidth($width);
                    $states[] = $page->$getter();
                }
                $states[] = $page->originalWidth();
                $states[] = (new Filament\Tests\Upgrade\Fixtures\PageWidth\Profile)->$getter();
                $states[] = (new Filament\Tests\Upgrade\Fixtures\PageWidth\GetterOnly)->$getter();
                $states[] = Filament\Tests\Upgrade\Fixtures\PageWidth\width(null);
                $states[] = (new Filament\Tests\Upgrade\Fixtures\PageWidth\Unrelated)->getMaxWidth();
                echo json_encode($states, JSON_THROW_ON_ERROR);
                PHP);

            $legacy = new Process([PHP_BINARY, $directory . '/verify.php', $root, 'legacy'], $root);
            $legacy->mustRun();
            $this->assertSame('["fallback-width","sm","custom-width","","fallback-width","fallback-width","xl","md",null,"unrelated"]', $legacy->getOutput());

            $command = [PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/app', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar'];

            foreach ([false, true] as $dryRun) {
                (new Process([...$command, ...($dryRun ? ['--dry-run'] : [])], $root))->setTimeout(120)->mustRun();
            }

            $migrated = new Process([PHP_BINARY, $directory . '/verify.php', $root, 'migrated'], $root);
            $migrated->mustRun();
            $this->assertSame($legacy->getOutput(), $migrated->getOutput());
            $this->assertSame('', $migrated->getErrorOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
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
