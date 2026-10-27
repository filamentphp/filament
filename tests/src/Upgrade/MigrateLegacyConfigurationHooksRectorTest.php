<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class MigrateLegacyConfigurationHooksRectorTest extends AbstractRectorTestCase
{
    public function test_migrates_configuration_declarations_and_filter_reads(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/configuration_hooks.php.inc');
    }

    public function test_removes_redundant_action_infolist_traits(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/action_infolist_trait.php.inc');
    }

    public function test_preserves_ambiguous_configuration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/configuration_hooks_manual.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
