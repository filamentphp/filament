<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameLegacyQueryBuilderClassesTest extends AbstractRectorTestCase
{
    public function test_migrates_legacy_constraints_operators_and_traits_without_changing_the_table_filter(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/legacy_query_builder_classes.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
