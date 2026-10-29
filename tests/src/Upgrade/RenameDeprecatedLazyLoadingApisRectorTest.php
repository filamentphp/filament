<?php

namespace Filament\Tests\Upgrade;

use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\CustomManager;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\EagerWidget;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\GetterOnlyManager;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\GetterOnlyWidget;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\GroupedProperties;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\InheritedMutableWidget;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\LazyAccountWidget;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\LazyFilamentInfoWidget;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\MutableWidget;
use Filament\Upgrade\Rector\RenameDeprecatedLazyLoadingApisRector;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Database\Eloquent\Model;
use PHPStan\Reflection\ReflectionProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class RenameDeprecatedLazyLoadingApisRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector's fixture cache bypass and PHPUnit subprocesses require this bootstrap constant.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_distinguishes_lazy_loading_from_blur_binding(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_calls.php.inc');
    }

    public function test_migrates_local_declarations_and_references_together(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_declarations.php.inc');

        $this->assertMigratedDeclarations();
    }

    public function test_preserves_collisions_and_ambiguous_custom_behavior(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_manual_boundaries.php.inc');
    }

    public function test_migrates_static_property_reference_targets(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_reference_target.php.inc');

        [, $expectedFixture] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/deprecated_lazy_loading_reference_target.php.inc'), 2);
        eval(substr($expectedFixture, strlen('<?php')));

        $this->assertFalse(Fixtures\LazyLoadingReferenceTarget\ReferenceTargetWidget::isLazyLoaded());
        (new Fixtures\LazyLoadingReferenceTarget\ReferenceTargetWidget)->configure(true);
        $this->assertTrue(Fixtures\LazyLoadingReferenceTarget\ReferenceTargetWidget::isLazyLoaded());
    }

    public function test_preserves_foreach_property_targets(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_foreach_targets.php.inc');
    }

    public function test_migrates_inherited_property_mutations_across_configured_files(): void
    {
        $directory = sys_get_temp_dir() . '/filament-lazy-inheritance-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);

        try {
            file_put_contents($directory . '/BaseWidget.php', <<<'PHP'
                <?php

                namespace LazyInheritance;

                class BaseWidget extends \Filament\Widgets\Widget
                {
                    protected static bool $isLazy = false;
                }
                PHP);
            file_put_contents($directory . '/ReportWidget.php', <<<'PHP'
                <?php

                namespace LazyInheritance;

                class ReportWidget extends BaseWidget
                {
                    public static function configure(bool $value): void
                    {
                        static::$isLazy = $value;
                    }

                    public static function inspect(): bool
                    {
                        return static::$isLazy;
                    }

                    public static function bind(bool &$value): void
                    {
                        static::$isLazy =& $value;
                    }

                    public static function toggle(): void
                    {
                        $reference = &static::$isLazy;
                        $reference = ! $reference;
                    }
                }
                PHP);
            $originalBase = file_get_contents($directory . '/BaseWidget.php');
            $originalReport = file_get_contents($directory . '/ReportWidget.php');
            $rule = var_export(RenameDeprecatedLazyLoadingApisRector::class, true);
            file_put_contents($directory . '/rector.php', <<<PHP
                <?php

                return \Rector\Config\RectorConfig::configure()
                    ->withPaths([__DIR__ . '/BaseWidget.php', __DIR__ . '/ReportWidget.php'])
                    ->withRules([{$rule}]);
                PHP);

            $runUpgrade = function (bool $dryRun = false) use ($root, $directory): void {
                $upgrade = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php',
                    '--clear-cache', '--no-progress-bar', ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $upgrade->setTimeout(120);
                $upgrade->run();
                $this->assertSame(0, $upgrade->getExitCode(), $upgrade->getOutput() . $upgrade->getErrorOutput());
            };

            for ($pass = 1; $pass <= 2; $pass++) {
                $runUpgrade(dryRun: $pass > 1);

                $runtime = new Process([PHP_BINARY, '-r', <<<'PHP'
                    require $argv[1] . '/vendor/autoload.php';
                    require $argv[2] . '/BaseWidget.php';
                    require $argv[2] . '/ReportWidget.php';
                    $values = [LazyInheritance\ReportWidget::isLazyLoaded(), LazyInheritance\ReportWidget::inspect()];
                    LazyInheritance\ReportWidget::configure(true);
                    $values[] = LazyInheritance\ReportWidget::isLazyLoaded();
                    LazyInheritance\ReportWidget::toggle();
                    $values[] = LazyInheritance\ReportWidget::isLazyLoaded();
                    $configuration = true;
                    LazyInheritance\ReportWidget::bind($configuration);
                    $values[] = LazyInheritance\ReportWidget::isLazyLoaded();
                    $configuration = false;
                    $values[] = LazyInheritance\ReportWidget::isLazyLoaded();
                    echo json_encode($values);
                    PHP, $root, $directory], $root);
                $runtime->run();
                $this->assertSame(0, $runtime->getExitCode(), $runtime->getErrorOutput());
                $this->assertSame([false, false, true, false, true, false], json_decode($runtime->getOutput(), associative: true));
            }

            // A canonical descendant setting keeps the shared legacy property separate.
            $collisionReport = str_replace("{\n    public static function configure", "{\n    protected static ?bool \$isLazyLoaded = true;\n\n    public static function configure", $originalReport);
            $this->assertNotSame($originalReport, $collisionReport);
            file_put_contents($directory . '/BaseWidget.php', $originalBase);
            file_put_contents($directory . '/ReportWidget.php', $collisionReport);
            $runUpgrade();
            $this->assertSame($originalBase, file_get_contents($directory . '/BaseWidget.php'));
            $this->assertSame($collisionReport, file_get_contents($directory . '/ReportWidget.php'));

            // Do not migrate an inherited reference while its custom parent is outside the paths.
            file_put_contents($directory . '/ReportWidget.php', $originalReport);
            file_put_contents($directory . '/rector.php', <<<PHP
                <?php

                return \Rector\Config\RectorConfig::configure()
                    ->withBootstrapFiles([__DIR__ . '/BaseWidget.php'])
                    ->withPaths([__DIR__ . '/ReportWidget.php'])
                    ->withRules([{$rule}]);
                PHP);
            $runUpgrade();
            $this->assertSame($originalBase, file_get_contents($directory . '/BaseWidget.php'));
            $this->assertSame($originalReport, file_get_contents($directory . '/ReportWidget.php'));
        } finally {
            (new Filesystem)->remove($directory);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_migrates_with_legacy_aliases_and_replacements_present(): void
    {
        require_once __DIR__ . '/Fixtures/LegacyLazyLoading/Widget.php';

        try {
            $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_legacy_aliases.php.inc');

            $this->assertSame(
                __DIR__ . '/Fixtures/LegacyLazyLoading/Widget.php',
                $this->make(ReflectionProvider::class)->getClass('Filament\\Widgets\\Widget')->getFileName(),
            );

            $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_declarations.php.inc');
            $this->assertMigratedDeclarations();
        } finally {
            // Rector cleans only the last input when a test uses multiple fixtures.
            $generatedFixture = __DIR__ . '/Fixtures/deprecated_lazy_loading_legacy_aliases.php';

            if (is_file($generatedFixture)) {
                unlink($generatedFixture);
            }
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }

    private function assertMigratedDeclarations(): void
    {
        [, $expectedFixture] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/deprecated_lazy_loading_declarations.php.inc'), 2);
        eval(substr($expectedFixture, strlen('<?php')));

        $this->assertFalse(EagerWidget::isLazyLoaded());
        $this->assertTrue((new EagerWidget)->inspect());
        $this->assertTrue(GetterOnlyWidget::isLazyLoaded());
        $this->assertTrue(GetterOnlyManager::isLazyLoaded());
        $record = new class extends Model {};
        $this->assertFalse(GetterOnlyManager::isBadgeLazyLoaded($record, 'Page'));
        $this->assertTrue(CustomManager::isLazyLoaded());
        $this->assertFalse(CustomManager::isBadgeLazyLoaded($record, 'Page'));
        $this->assertFalse(GroupedProperties::isLazyLoaded());
        $this->assertFalse((new GroupedProperties)->inspect());
        $this->assertFalse(AccountWidget::isLazyLoaded());
        $this->assertFalse(FilamentInfoWidget::isLazyLoaded());
        $this->assertTrue(LazyAccountWidget::isLazyLoaded());
        $this->assertTrue(LazyFilamentInfoWidget::isLazyLoaded());
        $this->assertFalse(MutableWidget::isLazyLoaded());
        InheritedMutableWidget::configure(false);
        $this->assertTrue(MutableWidget::isLazyLoaded());
        $this->assertTrue(InheritedMutableWidget::isLazyLoaded());
        InheritedMutableWidget::configure(true);
        $this->assertFalse(InheritedMutableWidget::isLazyLoaded());
        $configuration = true;
        InheritedMutableWidget::bind($configuration);
        $this->assertTrue(InheritedMutableWidget::isLazyLoaded());
        $configuration = false;
        $this->assertFalse(MutableWidget::isLazyLoaded());
    }
}
