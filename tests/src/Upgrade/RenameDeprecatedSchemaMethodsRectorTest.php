<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedSchemaMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_schema_calls_without_changing_unrelated_or_custom_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_schema_methods.php.inc');
    }

    public function test_preserves_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_schema_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
