<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedPanelMethodsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_renames_panel_and_navigation_calls_without_changing_arguments(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_panel_methods.php.inc');
    }

    public function test_preserves_custom_dispatch_and_unrelated_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_panel_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
