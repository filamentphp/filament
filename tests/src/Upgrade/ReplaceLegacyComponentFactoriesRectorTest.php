<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceLegacyComponentFactoriesRectorTest extends AbstractRectorTestCase
{
    public function test_preserves_column_factories_and_tag_list_limits(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_column_factories.php.inc');
    }

    public function test_preserves_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_column_manual_boundaries.php.inc');
    }

    public function test_preserves_form_factories_with_their_defaults_and_content_aliases(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_form_factories.php.inc');
    }

    public function test_preserves_form_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_form_manual_boundaries.php.inc');
    }

    public function test_preserves_factories_in_files_registering_global_configuration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_form_global_configuration.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
