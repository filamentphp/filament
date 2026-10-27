<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameSchemaComponentHooksRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_component_hooks_and_their_calls_together(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_component_hooks.php.inc');
    }

    public function test_migrates_configuration_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_configuration_calls.php.inc');
    }

    public function test_preserves_unrelated_configuration_contracts(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_configuration_boundaries.php.inc');
    }

    public function test_preserves_phpdoc_union_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_phpdoc_boundaries.php.inc');
    }

    public function test_preserves_class_string_union_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_class_string_union_boundary.php.inc');
    }

    public function test_leaves_cross_file_custom_parent_dispatch_for_manual_migration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_cross_file_hook.php.inc');
    }

    public function test_preserves_ambiguous_dispatch_for_manual_migration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_component_hook_boundaries.php.inc');
    }

    public function test_preserves_overrides_hidden_by_union_normalization(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_hook_unions.php.inc');
    }

    public function test_combines_legacy_operator_class_and_hook_renames(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_legacy_operator_hook.php.inc');
    }

    public function test_migrates_only_supported_schema_lookup_arguments(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_lookup_arguments.php.inc');
    }

    public function test_preserves_custom_lookup_arguments_hidden_by_union_normalization(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_lookup_union_boundary.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
