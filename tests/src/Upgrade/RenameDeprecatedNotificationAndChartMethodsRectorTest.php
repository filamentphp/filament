<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

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

    public function test_migrates_notification_triggers_with_the_full_ruleset(): void
    {
        $directory = sys_get_temp_dir() . '/filament-notification-triggers-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);

        try {
            foreach (['notification_triggers', 'notification_trigger_boundaries'] as $fixture) {
                $original = file_get_contents(__DIR__ . '/Fixtures/ReconciledContractMethods/' . $fixture . '.php.inc');
                [$before, $expected] = array_pad(explode("-----\n", $original, 2), 2, $original);
                file_put_contents($directory . '/Notifications.php', $before);

                $command = [PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/Notifications.php', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar'];
                $migration = new Process($command, $root);
                $migration->setTimeout(60)->mustRun();

                $this->assertSame($expected, file_get_contents($directory . '/Notifications.php'));

                $rerun = new Process([...$command, '--dry-run'], $root);
                $rerun->setTimeout(60)->mustRun();
                $this->assertSame($expected, file_get_contents($directory . '/Notifications.php'));
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
