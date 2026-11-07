<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class WidenAvatarProviderParameterTypeRectorTest extends AbstractRectorTestCase
{
    public function test_widens_avatar_providers_without_narrowing_compatible_signatures(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/avatar_providers.php.inc');
    }

    public function test_widens_implementation_owners_and_related_overrides_together(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/avatar_provider_owners.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
