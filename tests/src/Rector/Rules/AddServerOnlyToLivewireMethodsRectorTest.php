<?php

namespace Filament\Tests\Rector\Rules;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class AddServerOnlyToLivewireMethodsRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/AddServerOnlyToLivewireMethods/methods.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/AddServerOnlyToLivewireMethods/config.php';
    }
}
