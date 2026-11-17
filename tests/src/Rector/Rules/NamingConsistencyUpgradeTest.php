<?php

namespace Filament\Tests\Rector\Rules;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class NamingConsistencyUpgradeTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/NamingConsistencyUpgrade/names.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/NamingConsistencyUpgrade/config.php';
    }
}
