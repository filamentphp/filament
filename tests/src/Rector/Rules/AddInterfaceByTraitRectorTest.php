<?php

namespace Filament\Tests\Rector\Rules;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class AddInterfaceByTraitRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/AddInterfaceByTrait/classes.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/AddInterfaceByTrait/config.php';
    }
}
