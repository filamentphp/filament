<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedTableMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_table_calls_without_changing_named_arguments_or_unrelated_and_custom_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_table_methods.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
