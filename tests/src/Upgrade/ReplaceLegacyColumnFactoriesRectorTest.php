<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceLegacyColumnFactoriesRectorTest extends AbstractRectorTestCase
{
    public function test_migrates_direct_factories_and_tag_list_limits(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_column_factories.php.inc');
    }

    public function test_preserves_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_column_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
