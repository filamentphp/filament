<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedFormComponentMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_form_component_calls_without_changing_unrelated_or_custom_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_form_component_methods.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
