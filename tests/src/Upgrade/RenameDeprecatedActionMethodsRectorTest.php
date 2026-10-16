<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedActionMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_action_calls_and_named_arguments_without_changing_unrelated_or_custom_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_action_methods.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
