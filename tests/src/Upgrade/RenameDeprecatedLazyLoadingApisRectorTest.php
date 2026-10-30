<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedLazyLoadingApisRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Disable Rector's persistent fixture cache when running through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_distinguishes_lazy_loading_from_blur_binding(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_calls.php.inc');
    }

    public function test_migrates_configuration_declarations_and_parent_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_declarations.php.inc');
    }

    public function test_preserves_custom_names_and_collisions(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_lazy_loading_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
