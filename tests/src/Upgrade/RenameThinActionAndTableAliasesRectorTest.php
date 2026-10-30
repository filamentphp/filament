<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameThinActionAndTableAliasesRectorTest extends AbstractRectorTestCase
{
    public function test_renames_typed_action_and_table_calls_without_runtime_adapters(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/thin_action_table_aliases.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/thin_action_table_aliases_config.php';
    }
}
