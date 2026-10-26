<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class ReplaceTypedChartWidgetsRectorTest extends AbstractRectorTestCase
{
    public function test_preserves_chart_types_and_independent_overrides(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/typed_chart_widgets.php.inc');
    }

    public function test_leaves_traits_parent_calls_and_other_class_references_for_manual_migration(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/typed_chart_widget_manual_boundaries.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
