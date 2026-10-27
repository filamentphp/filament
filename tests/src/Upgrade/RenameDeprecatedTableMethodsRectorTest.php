<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedTableMethodsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_renames_table_calls_without_changing_named_arguments_or_unrelated_and_custom_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_table_methods.php.inc');
    }

    public function test_renames_sort_hook_calls_without_changing_custom_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_sort_hook.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
