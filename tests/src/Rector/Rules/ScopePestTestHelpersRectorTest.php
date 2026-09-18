<?php

namespace Filament\Tests\Rector\Rules;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ScopePestTestHelpersRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): Iterator
    {
        foreach (array_merge(
            glob(__DIR__ . '/Fixtures/ScopePestTestHelpers/*.php.inc'),
            glob(__DIR__ . '/Fixtures/ScopePestTestHelpers/*/*.php.inc'),
        ) as $filePath) {
            yield basename($filePath, '.php.inc') => [$filePath];
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/ScopePestTestHelpers/config.php';
    }
}
