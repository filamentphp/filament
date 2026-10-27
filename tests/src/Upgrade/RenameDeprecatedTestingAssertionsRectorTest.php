<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedTestingAssertionsRectorTest extends AbstractRectorTestCase
{
    public function test_migrates_testing_macros_without_the_removed_aliases_installed(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_testing_assertions.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
