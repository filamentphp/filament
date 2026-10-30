<?php

namespace Filament\Tests\Upgrade;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Tables\Concerns\CanGroupRecords;
use Filament\Tables\Concerns\HasColumnManager;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class RenameThinActionAndTableAliasesRectorTest extends TestCase
{
    public function test_migrates_real_trait_consumers_with_removed_aliases_and_preserves_runtime_contracts(): void
    {
        $root = dirname(__DIR__, 3);
        $fixtures = __DIR__ . '/Fixtures/ThinActionAndTableAliases';
        $directory = sys_get_temp_dir() . '/filament-thin-aliases-' . bin2hex(random_bytes(8));
        mkdir($directory);

        try {
            foreach (['Owner.php', 'Consumers.php'] as $file) {
                copy($fixtures . '/' . $file, $directory . '/' . $file);
            }

            $baseline = $this->runtime($root, $fixtures, $directory, 'legacy');
            $unmigrated = new Process([PHP_BINARY, $fixtures . '/runtime.php', $root, $directory, 'canonical'], $root);
            $unmigrated->run();
            $this->assertNotSame(0, $unmigrated->getExitCode());
            $this->assertStringContainsString('mountedActionHasForm', $unmigrated->getErrorOutput());
            $set = var_export($root . '/packages/upgrade/src/rector.php', true);
            file_put_contents($directory . '/rector.php', <<<PHP
                <?php

                return \Rector\Config\RectorConfig::configure()
                    ->withPaths([__DIR__ . '/Owner.php', __DIR__ . '/Consumers.php'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withSets([{$set}]);
                PHP);

            $firstMigration = null;

            for ($cycle = 1; $cycle <= 2; $cycle++) {
                if ($cycle > 1) {
                    // Reintroduce the original input, rather than only rerunning
                    // already-migrated files against fresh Rector processes.
                    foreach (['Owner.php', 'Consumers.php'] as $file) {
                        copy($fixtures . '/' . $file, $directory . '/' . $file);
                    }

                    $this->assertSame($baseline, $this->runtime($root, $fixtures, $directory, 'legacy'));
                }

                for ($pass = 1; $pass <= 2; $pass++) {
                    $upgrade = new Process([
                        PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php',
                        '--clear-cache', '--no-progress-bar', ...($pass > 1 ? ['--dry-run'] : []),
                    ], $root);
                    $upgrade->setTimeout(120);
                    $upgrade->run();
                    $this->assertSame(0, $upgrade->getExitCode(), $upgrade->getOutput() . $upgrade->getErrorOutput());
                    $this->assertStringNotContainsString('parent::updatedToggledTableColumns', file_get_contents($directory . '/Consumers.php'));
                    $this->assertSame($baseline, $this->runtime($root, $fixtures, $directory, 'canonical'));
                }

                $migration = [file_get_contents($directory . '/Owner.php'), file_get_contents($directory . '/Consumers.php')];

                if ($firstMigration !== null) {
                    $this->assertSame($firstMigration, $migration);
                }

                $firstMigration = $migration;
            }

            $migrated = file_get_contents($directory . '/Consumers.php');
            $this->assertStringNotContainsString('parent::mountedActionHasForm', $migrated);
            $this->assertStringNotContainsString('parent::updatedToggledTableColumns', $migrated);
            $this->assertStringContainsString('public function updatedToggledTableColumns(): void', $migrated);
            $this->assertStringContainsString('$owner->updatedToggledTableColumns();', $migrated);
            $this->assertStringContainsString('$nullable?->applyTableColumnManager', $migrated);
        } finally {
            (new Filesystem)->remove($directory);
        }
    }

    public function test_removes_only_the_thin_product_aliases_and_preserves_visibility_and_exposure(): void
    {
        foreach ([
            InteractsWithActions::class => ['mountedActionHasForm', 'getMountedActionForm'],
            HasColumnManager::class => ['updatedToggledTableColumns', 'getToggledTableColumnsSessionKey'],
            CanGroupRecords::class => ['updatedTableGroupColumn'],
        ] as $trait => $methods) {
            foreach ($methods as $method) {
                $this->assertFalse(method_exists($trait, $method));
            }
        }

        $this->assertTrue((new ReflectionMethod(InteractsWithActions::class, 'getMountedActionSchema'))->isProtected());
        $this->assertNotEmpty((new ReflectionMethod(InteractsWithActions::class, 'mountedActionHasSchema'))->getAttributes(Unexposed::class));
        $this->assertNotEmpty((new ReflectionMethod(HasColumnManager::class, 'getTableColumnsSessionKey'))->getAttributes(Unexposed::class));
        $this->assertNotEmpty((new ReflectionMethod(HasColumnManager::class, 'applyTableColumnManager'))->getAttributes(Exposed::class));
        $this->assertNotEmpty((new ReflectionMethod(CanGroupRecords::class, 'updatedTableGrouping'))->getAttributes(Unexposed::class));
    }

    public function test_leaves_unrelated_dynamic_and_custom_dispatch_boundaries_unchanged(): void
    {
        $root = dirname(__DIR__, 3);
        $fixtures = __DIR__ . '/Fixtures/ThinActionAndTableAliases';
        $directory = sys_get_temp_dir() . '/filament-thin-alias-boundaries-' . bin2hex(random_bytes(8));
        mkdir($directory);

        try {
            foreach (['Owner.php', 'Boundaries.php'] as $file) {
                copy($fixtures . '/' . $file, $directory . '/' . $file);
            }

            $upgrade = new Process([
                PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory,
                '--config', __DIR__ . '/Fixtures/thin_action_table_aliases_config.php',
                '--clear-cache', '--no-progress-bar',
            ], $root);
            $upgrade->setTimeout(120);
            $upgrade->run();
            $this->assertSame(0, $upgrade->getExitCode(), $upgrade->getOutput() . $upgrade->getErrorOutput());
            $this->assertSame(file_get_contents($fixtures . '/Boundaries.php'), file_get_contents($directory . '/Boundaries.php'));
        } finally {
            (new Filesystem)->remove($directory);
        }
    }

    private function runtime(string $root, string $fixtures, string $directory, string $mode): array
    {
        $runtime = new Process([PHP_BINARY, $fixtures . '/runtime.php', $root, $directory, $mode], $root);
        $runtime->run();
        $this->assertSame(0, $runtime->getExitCode(), $runtime->getErrorOutput());

        return json_decode($runtime->getOutput(), associative: true, flags: JSON_THROW_ON_ERROR);
    }
}
