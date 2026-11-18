<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Support\Number;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceDeprecatedNumberHelpersRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('fixtures')]
    public function test_rewrites_supported_numbers_without_signature_adapters(string $fixture): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
    }

    public static function fixtures(): array
    {
        return [['deprecated_number_helpers'], ['deprecated_number_helpers_manual'], ['number_helper_computed'], ['deprecated_number_helpers_strict']];
    }

    public function test_formats_supported_expressions_with_real_laravel_formatters(): void
    {
        [, $migrated] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/number_helper_computed.php.inc'), 2);
        eval('?>' . $migrated);

        foreach (['en', 'de'] as $locale) {
            Number::withLocale($locale, function (): void {
                foreach ([0, 100, -100] as $divisor) {
                    $this->assertSame([
                        Number::currency(1234.5 / 100, 'USD'),
                        Number::currency($divisor ? 1234.5 / $divisor : 1234.5, 'USD'),
                        Number::currency(1234.5 / 100, 'USD'),
                        Number::format(1234.5),
                        Number::format(1234.5),
                        Number::currency(1234.5 / 100, 'USD'),
                    ], Fixtures\ComputedNumberHelpers\results(1234.5, 'USD', $divisor));
                }
            });
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
