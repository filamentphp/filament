<?php

namespace Filament\Tests\Upgrade;

use Filament\Forms\Components\Field;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class PersistentUpgradeSetTest extends TestCase
{
    public function test_the_complete_set_migrates_fixtures_and_remains_stable_in_fresh_processes(): void
    {
        $directory = sys_get_temp_dir() . '/filament-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);

        foreach (['app', 'config', 'routes', 'tests'] as $sourceDirectory) {
            mkdir($directory . '/' . $sourceDirectory);
        }

        $originalFiles = [];
        $expectedFiles = [];

        try {
            foreach (glob(__DIR__ . '/Fixtures/*.php.inc') as $fixture) {
                $parts = explode("-----\n", file_get_contents($fixture), 2);
                $sourceDirectory = match (basename($fixture)) {
                    'legacy_query_builder_classes.php.inc' => 'config',
                    'deprecated_enum_constants.php.inc' => 'routes',
                    'persistent_upload_restriction.php.inc' => 'tests',
                    default => 'app',
                };
                $path = $directory . '/' . $sourceDirectory . '/' . basename($fixture, '.inc');
                file_put_contents($path, $parts[0]);
                $originalFiles[$path] = $parts[0];
                $expectedFiles[$path] = trim($parts[1] ?? $parts[0]);
            }

            $root = dirname(__DIR__, 3);
            $configuration = $directory . '/rector.php';
            $set = var_export($root . '/packages/upgrade/src/rector.php', true);
            file_put_contents($configuration, <<<PHP
                <?php

                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([
                        __DIR__ . '/app',
                        __DIR__ . '/config',
                        __DIR__ . '/routes',
                        __DIR__ . '/tests',
                    ])
                    ->withCache(__DIR__ . '/cache')
                    ->withSets([{$set}]);
                PHP);

            $runRector = function (bool $dryRun) use ($root, $configuration): Process {
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process',
                    '--config', $configuration, '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(120);
                $process->run();

                return $process;
            };

            for ($pass = 1; $pass <= 3; $pass++) {
                $process = $runRector($pass > 1);
                $this->assertSame(0, $process->getExitCode(), "Pass {$pass}:\n" . $process->getOutput() . $process->getErrorOutput());

                foreach ($expectedFiles as $path => $expected) {
                    $this->assertSame($expected, trim(file_get_contents($path)), "Pass {$pass}: " . basename($path));
                }

                require_once $directory . '/app/behavioral_configuration.php';
                $this->assertBehavioralConfiguration();

                $runtime = new Process([PHP_BINARY, '-r', <<<'PHP'
                    require $argv[1] . '/vendor/autoload.php';
                    require $argv[2] . '/app/deprecated_number_helpers.php';
                    require $argv[2] . '/app/deprecated_exposed_attribute.php';
                    $results = \Illuminate\Support\Number::withLocale('en', fn () =>
                        \Illuminate\Support\Number::withCurrency('EUR', fn () =>
                            \Filament\Tests\Upgrade\Fixtures\NumberHelpers\results()));
                    foreach (['increment', 'inherited', 'current'] as $method) {
                        $attribute = (new ReflectionMethod(\Filament\Tests\Upgrade\Fixtures\ExposedAttribute\Counter::class, $method))->getAttributes()[0]->newInstance();
                        if (! $attribute instanceof \Filament\Support\Components\Attributes\Exposed) {
                            throw new RuntimeException('Exposure was lost.');
                        }
                    }
                    echo json_encode([
                        array_slice($results, 0, 9),
                        \Illuminate\Support\Number::withLocale('de', fn () => \Filament\Tests\Upgrade\Fixtures\NumberHelpers\results()[0]),
                        function_exists('Filament\\Support\\format_money') || function_exists('Filament\\Support\\format_number') ||
                            class_exists('Filament\\Support\\Components\\Attributes\\ExposedLivewireMethod') ||
                            interface_exists('Filament\\Schemas\\Components\\Contracts\\CanConcealComponents'),
                    ]);
                    PHP, $root, $directory], $root);
                $runtime->mustRun();
                $this->assertSame([
                    ['1,234.5', '-1,234.5', '42', '$1,234.50', '-$42.50', '£125.00', '-€1.25', '$987.65', '€12.50'],
                    '1.234,5',
                    false,
                ], json_decode($runtime->getOutput(), associative: true), "Runtime pass {$pass}");
            }

            // Reintroduce every migratable fixture, not just a subset of rules.
            $reintroducedFiles = [];

            foreach ($originalFiles as $path => $input) {
                if (trim($input) === $expectedFiles[$path]) {
                    continue;
                }

                file_put_contents($path, $input);
                $reintroducedFiles[$path] = $input;
            }

            $process = $runRector(true);
            $this->assertSame(2, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

            foreach ($reintroducedFiles as $path => $input) {
                $this->assertSame($input, file_get_contents($path));
            }

            $process = $runRector(false);
            $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

            foreach ($expectedFiles as $path => $expected) {
                $this->assertSame($expected, trim(file_get_contents($path)));
            }

            for ($pass = 1; $pass <= 2; $pass++) {
                $process = $runRector(true);
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    private function assertBehavioralConfiguration(): void
    {
        $components = \Filament\Tests\Upgrade\Fixtures\BehavioralConfiguration\components();

        Schema::make(Livewire::make())->components(array_filter($components, static fn ($component): bool => $component instanceof Field))->getComponents();

        $this->assertFalse($components[0]->isPreviewable());
        $this->assertTrue($components[1]->isPreviewable());
        $this->assertSame('16:9', $components[1]->getAutomaticallyCropImagesAspectRatio());
        $this->assertFalse($components[2]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertTrue($components[3]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertFalse($components[4]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertNull($components[4]->getImageAspectRatio());
        $this->assertSame([false, true, false], [$components[5]->isAddable(), $components[5]->isDeletable(), $components[5]->isReorderable()]);
        $this->assertSame([true, false, true], [$components[6]->isAddable(), $components[6]->isDeletable(), $components[6]->isReorderable()]);
        $this->assertSame([false, true, true, false], [$components[7]->isAddable(), $components[7]->isDeletable(), $components[7]->canEditKeys(), $components[7]->canEditValues()]);
        $this->assertSame([null, 'off'], [$components[8]->getAutocomplete(), $components[8]->getAutocapitalize()]);
        $this->assertSame(['off', null], [$components[9]->getAutocomplete(), $components[9]->getAutocapitalize()]);
        $this->assertNull($components[10]->getAutocomplete());
        $this->assertFalse($components[11]->canSelectPlaceholder());
        $this->assertTrue($components[12]->canSelectPlaceholder());
        $this->assertSame('H:i', $components[13]->getInternalFormat());
        $this->assertSame(Heroicon::Calendar, $components[13]->getSuffixIcon());
        $this->assertTrue($components[13]->isSuffixInline());
        $this->assertSame(Heroicon::Calendar, $components[14]->getSuffixIcon());
        $this->assertSame('heroicon-o-clock', $components[15]->getSuffixIcon());
        $this->assertSame('heroicon-o-star', $components[16]->getSuffixIcon());
        $this->assertFalse($components[16]->isSuffixInline());
        $this->assertSame(Alignment::Start, $components[17]->getModalAlignment());
        $this->assertSame(Alignment::End, $components[18]->getModalAlignment());
        $this->assertSame(Alignment::Center, $components[19]->getModalAlignment());
        $this->assertFalse($components[20]->canCreateAnother());
        $this->assertTrue($components[21]->canAttachAnother());
        $this->assertFalse($components[22]->canAssociateAnother());
        $this->assertFalse($components[23]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertSame('16:9', $components[23]->getImageAspectRatio());
        $this->assertSame(Alignment::Start, $components[24]->getModalAlignment());
        $this->assertSame(['on', 'words'], [$components[25]->getAutocomplete(), $components[25]->getAutocapitalize()]);

        foreach ([
            [0, 'disablePreview'], [1, 'imageCropAspectRatio'],
            [5, 'disableItemCreation'], [5, 'disableItemDeletion'], [5, 'disableItemMovement'], [5, 'inset'],
            [6, 'disableItemCreation'], [6, 'disableItemDeletion'], [6, 'disableItemMovement'], [6, 'inset'],
            [7, 'disableAddingRows'], [7, 'disableDeletingRows'], [7, 'disableEditingKeys'], [7, 'disableEditingValues'],
            [8, 'disableAutocomplete'], [8, 'disableAutocapitalize'], [11, 'disablePlaceholderSelection'],
            [13, 'withoutDate'], [13, 'withoutSeconds'], [13, 'withoutTime'], [13, 'icon'],
            [17, 'centerModal'], [20, 'disableCreateAnother'], [21, 'disableAttachAnother'], [22, 'disableAssociateAnother'],
        ] as [$index, $method]) {
            $this->assertFalse(method_exists($components[$index], $method));
        }
    }
}
