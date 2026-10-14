<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedEnumConstantsTest extends AbstractRectorTestCase
{
    public function test_renames_deprecated_enum_constants_without_changing_other_constants_or_strings(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_enum_constants.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
