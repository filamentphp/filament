<?php

namespace Filament\Tests\Upgrade;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceDeprecatedBehavioralConfigurationRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('fixtures')]
    public function test_migrates_configuration_and_preserves_manual_boundaries(string $fixture): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
    }

    public static function fixtures(): array
    {
        return [['behavioral_configuration'], ['behavioral_configuration_manual'], ['behavioral_configuration_expressions']];
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
