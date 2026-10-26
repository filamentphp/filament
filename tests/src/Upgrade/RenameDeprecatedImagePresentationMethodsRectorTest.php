<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedImagePresentationMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_presentation_calls_without_changing_unrelated_or_custom_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_image_presentation_methods.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
