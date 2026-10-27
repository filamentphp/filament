<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedGeneratorAndDashboardMethodsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_renames_owned_calls_without_changing_arguments_or_references(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_generator_and_dashboard_methods.php.inc');
    }

    public function test_leaves_custom_dispatch_and_unrelated_methods_for_manual_migration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_generator_and_dashboard_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
