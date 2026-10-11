<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class WidenAvatarProviderParameterTypeRectorTest extends AbstractRectorTestCase
{
    public function test_widens_avatar_providers_without_narrowing_compatible_signatures(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/avatar_providers.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
