<?php

namespace Filament\Tests\Rector\Rules;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class MovePestTestClassesToEndRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): Iterator
    {
        yield from self::yieldFilesFromDirectory(__DIR__ . '/Fixtures/MovePestTestClassesToEnd');
    }

    public function test_does_not_change_files_outside_test_paths(): void
    {
        $temporaryFilePath = tempnam(sys_get_temp_dir(), 'filament-rector-');

        $this->assertNotFalse($temporaryFilePath);

        $fixtureFilePath = "{$temporaryFilePath}.php.inc";
        rename($temporaryFilePath, $fixtureFilePath);
        copy(__DIR__ . '/Fixtures/MovePestTestClassesToEnd/outside_test_paths.php.fixture', $fixtureFilePath);

        try {
            $this->doTestFile($fixtureFilePath);
        } finally {
            unlink($fixtureFilePath);
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/MovePestTestClassesToEnd/config.php';
    }
}
