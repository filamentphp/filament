<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedNotificationAndChartMethodsRectorTest extends AbstractRectorTestCase
{
    public function test_renames_calls_on_notifications_and_chart_filter_schema_consumers(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_notification_and_chart_methods.php.inc');
    }

    public function test_preserves_custom_dispatch_and_unrelated_receivers(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_notification_and_chart_manual_boundaries.php.inc');
    }

    public function test_uses_normalized_receiver_types_for_base_and_subclass_unions(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_notification_and_chart_normalized_unions.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
