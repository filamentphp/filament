<?php

namespace Filament\Tests\Rector\Rules;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class AddUnexposedToLivewireMethodsRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/AddUnexposedToLivewireMethods/methods.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/AddUnexposedToLivewireMethods/config.php';
    }
}
