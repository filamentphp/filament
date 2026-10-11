<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedRenderHookConstantsTest extends AbstractRectorTestCase
{
    public function test_renames_deprecated_render_hooks_without_changing_other_constants_or_strings(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_render_hooks.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
