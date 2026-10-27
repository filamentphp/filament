<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedColumnAndSupportMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_calls_only_on_known_owners_without_custom_overrides(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_and_support_methods.php.inc');
    }

    public function test_preserves_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_and_support_manual_boundaries.php.inc');
    }

    public function test_renames_the_copyable_trait_including_adaptations(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_copyable_trait.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
