<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceConvenienceClassesRectorTest extends AbstractRectorTestCase
{
    public function test_preserves_deprecated_factories_and_existing_chains(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/convenience_factories.php.inc');
    }

    public function test_preserves_class_references_and_global_configuration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/convenience_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
