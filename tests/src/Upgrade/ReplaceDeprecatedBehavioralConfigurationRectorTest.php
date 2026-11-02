<?php

namespace Filament\Tests\Upgrade;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceDeprecatedBehavioralConfigurationRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Refresh Rector's source locator for application subclasses between fixtures.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    #[DataProvider('fixtures')]
    public function test_migrates_configuration_and_preserves_manual_boundaries(string $fixture): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
    }

    public static function fixtures(): array
    {
        return [['behavioral_configuration'], ['behavioral_configuration_manual'], ['behavioral_configuration_expressions'], ['behavioral_configuration_inset']];
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/behavioral_configuration_config.php';
    }
}
