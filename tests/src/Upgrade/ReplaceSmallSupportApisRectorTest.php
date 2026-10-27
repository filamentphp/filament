<?php

namespace Filament\Tests\Upgrade;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceSmallSupportApisRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('fixtures')]
    public function test_migrates_only_safe_support_references_without_legacy_symbols(string $fixture): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
    }

    public static function fixtures(): array
    {
        return array_map(static fn (string $fixture): array => [$fixture], [
            'deprecated_number_helpers',
            'deprecated_number_helpers_manual',
            'deprecated_number_helpers_overrides',
            'deprecated_number_helpers_strict',
            'number_helper_variables',
            'deprecated_exposed_attribute',
            'deprecated_exposed_attribute_reflection',
            'deprecated_exposed_attribute_types',
            'obsolete_concealment_and_widget_filters',
        ]);
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
