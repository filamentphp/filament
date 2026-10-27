<?php

namespace Filament\Tests\Upgrade;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceLegacyPanelIntegrationRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('fixtures')]
    public function test_preserves_registration_contracts(string $fixture): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
    }

    public static function fixtures(): array
    {
        return [['panel_integrations'], ['panel_integrations_manual'], ['panel_registration'], ['menu_items']];
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
