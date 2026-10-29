<?php

namespace Filament\Tests\Upgrade;

use Filament\Widgets\ChartWidget;
use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use ReflectionMethod;
use Symfony\Component\Process\Process;

class ReplaceTypedChartWidgetsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_preserves_chart_types_and_independent_overrides(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/typed_chart_widgets.php.inc');
    }

    public function test_preserves_traits_parent_calls_and_other_class_references(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/typed_chart_widget_manual_boundaries.php.inc');
    }

    public function test_preserves_unresolved_traits(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/typed_chart_widget_unresolved_trait.php.inc');
    }

    public function test_preserves_shared_trait_consumers_and_inheritance(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/typed_chart_widget_consumers.php.inc');
    }

    public function test_migrated_widgets_match_original_runtime_behavior_without_legacy_parents(): void
    {
        $directory = sys_get_temp_dir() . '/filament-typed-charts-' . bin2hex(random_bytes(8));
        mkdir($directory);

        try {
            $root = dirname(__DIR__, 3);
            $configuration = $this->provideConfigFilePath();
            $originals = __DIR__ . '/Fixtures/TypedCharts/LegacyTypedChartWidgets.php';

            if (! class_exists('Filament\\Widgets\\BarChartWidget')) {
                require $originals;
            }

            foreach (['typed_chart_widgets', 'typed_chart_widget_consumers'] as $fixture) {
                $source = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.php.inc'), 2)[0];
                $originalSource = str_replace('Filament\\Tests\\Upgrade\\Fixtures\\', 'Filament\\Tests\\Upgrade\\OriginalCharts\\', $source);
                $originalWidgets = eval(substr($originalSource, 5));

                $fixtureDirectory = $directory . '/' . $fixture;
                mkdir($fixtureDirectory);
                $path = $fixtureDirectory . '/widgets.php';
                $migratedSource = str_replace('Filament\\Tests\\Upgrade\\Fixtures\\', 'Filament\\Tests\\Upgrade\\MigratedCharts\\', $source);

                if ($fixture === 'typed_chart_widget_consumers') {
                    // Resolve shared traits from a separate file, as in an application.
                    [$traits, $classes] = explode('class Sales extends', $migratedSource, 2);
                    file_put_contents($fixtureDirectory . '/traits.php', $traits);
                    $header = substr($traits, 0, strpos($traits, 'trait Data'));
                    $migratedSource = $header . 'class Sales extends' . $classes;
                }

                file_put_contents($path, $migratedSource);

                foreach ([false, true] as $dryRun) {
                    $process = new Process([
                        PHP_BINARY, $root . '/vendor/bin/rector', 'process', $fixtureDirectory,
                        '--config', $configuration, '--clear-cache', '--no-progress-bar',
                        ...($dryRun ? ['--dry-run'] : []),
                    ], $root);
                    $process->setTimeout(60);
                    $process->run();
                    $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
                }

                if ($fixture === 'typed_chart_widget_consumers') {
                    $this->assertSame($traits, file_get_contents($fixtureDirectory . '/traits.php'));
                    require $fixtureDirectory . '/traits.php';
                }

                $migratedWidgets = require $path;
                $this->assertSame(count($originalWidgets), count($migratedWidgets));

                foreach ($originalWidgets as $index => $originalWidget) {
                    $migratedWidget = $migratedWidgets[$index];
                    $this->assertSame($this->snapshot($originalWidget), $this->snapshot($migratedWidget));

                    if ($migratedWidget instanceof ChartWidget) {
                        foreach (['Bar', 'Bubble', 'Doughnut', 'Line', 'Pie', 'PolarArea', 'Radar', 'Scatter'] as $type) {
                            $this->assertNotContains('Filament\\Widgets\\' . $type . 'ChartWidget', class_parents($migratedWidget));
                        }
                    }
                }
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    /** @return array<string, mixed> */
    private function snapshot(object $widget): array
    {
        $snapshot = ['type' => (new ReflectionMethod($widget, 'getType'))->invoke($widget)];

        if ($widget instanceof ChartWidget) {
            $widget->mount();
            $snapshot['checksum'] = $widget->dataChecksum;
            $snapshot['data'] = (new ReflectionMethod($widget, 'getCachedData'))->invoke($widget);
            $snapshot['color'] = $widget->getColor();
            $snapshot['heading'] = $widget->getHeading();
        }

        foreach (['originalType', 'nestedType'] as $method) {
            if (method_exists($widget, $method)) {
                $snapshot[$method] = $widget->{$method}();
            }
        }

        return $snapshot;
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
