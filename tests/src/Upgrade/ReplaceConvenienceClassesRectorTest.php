<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceConvenienceClassesRectorTest extends AbstractRectorTestCase
{
    public function test_migrates_direct_factories_and_preserves_existing_chains(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/convenience_factories.php.inc');
    }

    public function test_renames_class_references_without_inserting_configuration_outside_direct_factories(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/convenience_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
