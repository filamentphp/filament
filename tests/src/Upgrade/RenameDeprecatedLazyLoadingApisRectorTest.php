<?php

namespace Filament\Tests\Upgrade;

use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\CustomManager;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\EagerWidget;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\GetterOnlyManager;
use Filament\Tests\Upgrade\Fixtures\LazyLoadingDeclarations\GetterOnlyWidget;
use Illuminate\Database\Eloquent\Model;
use PHPStan\Reflection\ReflectionProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

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
    }

    public function test_preserves_collisions_and_ambiguous_custom_behavior(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_manual_boundaries.php.inc');
    }

    public function test_preserves_static_property_reference_targets(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_reference_target.php.inc');
    }

    public function test_preserves_foreach_property_targets(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_foreach_targets.php.inc');
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_migrates_with_legacy_aliases_and_replacements_present(): void
    {
        require_once __DIR__ . '/Fixtures/LegacyLazyLoading/Widget.php';

        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_legacy_aliases.php.inc');

        $this->assertSame(
            __DIR__ . '/Fixtures/LegacyLazyLoading/Widget.php',
            $this->make(ReflectionProvider::class)->getClass('Filament\\Widgets\\Widget')->getFileName(),
        );
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
