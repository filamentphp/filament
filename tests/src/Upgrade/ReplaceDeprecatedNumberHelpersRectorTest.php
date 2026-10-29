<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class ReplaceDeprecatedNumberHelpersRectorTest extends AbstractRectorTestCase
{
    public function test_matches_pinned_5_helpers_with_real_laravel_formatters(): void
    {
        $directory = sys_get_temp_dir() . '/filament-number-helpers-' . bin2hex(random_bytes(8));
        $fixtures = ['number_helper_computed', 'deprecated_number_helpers_strict'];
        mkdir($directory);

        try {
            foreach ($fixtures as $fixture) {
                $this->doTestFile(__DIR__ . '/Fixtures/' . $fixture . '.php.inc');
                [$original] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.php.inc'), 2);
                file_put_contents($directory . '/' . $fixture . '-original.php', $original);
                file_put_contents($directory . '/' . $fixture . '-migrated.php', file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.php'));
            }

            $baseline = $this->runScenarios($directory, 'original');
            $migrated = $this->runScenarios($directory, 'migrated');

            $this->assertSame($baseline, $migrated);
            $this->assertFalse($baseline['formatter-failure']);
            $this->assertSame(['value', '$3,393.07'], $baseline['en']['weak'][0]);
            $this->assertSame(['value', '-$1,827.04'], $baseline['en']['weak'][1]);
            $this->assertSame(['value', '$23,751.50'], $baseline['en']['weak'][2]);
            $this->assertSame(['value', ''], $baseline['en']['weak']['false-formatter']);
            $this->assertSame(['value', '$23,751.50'], array_slice($baseline['en']['weak']['coerced-zero'], 0, 2));
            $this->assertSame(E_DEPRECATED, $baseline['en']['weak']['coerced-zero'][2][0][0]);
            $this->assertSame(['exception', 'TypeError'], $baseline['en']['strict'][3]);
            $this->assertSame(['exception', 'ArgumentCountError'], $baseline['en']['weak']['missing']);
            $this->assertSame(['exception', 'Error'], $baseline['en']['weak']['unknown']);
            $this->assertSame([
                'currency', 'divisor', 'amount',
                'amount', 'currency', 'divisor', 'extra',
                'invalid-currency', 'validated-last',
                'decimal', 'ignored-precision',
            ], $baseline['en']['weak']['events']);
            $this->assertNotSame($baseline['en']['weak'][0], $baseline['de']['weak'][0]);
        } finally {
            foreach (glob($directory . '/*') as $path) {
                unlink($path);
            }

            // Rector only removes the last input when multiple fixtures run in one test.
            foreach ($fixtures as $fixture) {
                $path = __DIR__ . '/Fixtures/' . $fixture . '.php';

                if (is_file($path)) {
                    unlink($path);
                }
            }

            rmdir($directory);
        }
    }

    private function runScenarios(string $directory, string $version): array
    {
        $root = dirname(__DIR__, 3);
        $process = new Process([PHP_BINARY, '-r', <<<'PHP'
            if ($argv[3] === 'original') {
                require $argv[1] . '/tests/src/Upgrade/Fixtures/NumberHelpers/pinned-5-helpers.php';
            }
            require $argv[1] . '/vendor/autoload.php';
            if (($argv[3] === 'migrated') && (function_exists('Filament\Support\format_money') || function_exists('Filament\Support\format_number'))) {
                throw new RuntimeException('Migrated calls must run without the removed helpers.');
            }
            require $argv[2] . '/number_helper_computed-' . $argv[3] . '.php';
            require $argv[2] . '/deprecated_number_helpers_strict-' . $argv[3] . '.php';
            $observe = static function (callable $operation): array {
                $diagnostics = [];
                set_error_handler(static function (int $severity, string $message) use (&$diagnostics): bool {
                    $diagnostics[] = [$severity, $message];

                    return true;
                });
                try {
                    $result = ['value', $operation()];
                } catch (Throwable $exception) {
                    $result = ['exception', $exception::class];
                } finally {
                    restore_error_handler();
                }

                return $diagnostics ? [...$result, $diagnostics] : $result;
            };
            $results = ['formatter-failure' => Illuminate\Support\Number::currency(23751.5, 'X')];
            foreach (['en', 'de'] as $locale) {
                $results[$locale] = Illuminate\Support\Number::withLocale($locale, static fn () =>
                    Illuminate\Support\Number::withCurrency('EUR', static fn () => [
                        'weak' => Filament\Tests\Upgrade\Fixtures\ComputedNumberHelpers\results($observe),
                        'strict' => Filament\Tests\Upgrade\Fixtures\StrictNumberHelpers\results($observe),
                    ]));
            }
            echo json_encode($results, JSON_THROW_ON_ERROR);
            PHP, $root, $directory, $version], $root);
        $process->mustRun();
        $this->assertSame('', $process->getErrorOutput());

        return json_decode($process->getOutput(), associative: true, flags: JSON_THROW_ON_ERROR);
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
