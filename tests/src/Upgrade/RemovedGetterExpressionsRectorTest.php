<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class RemovedGetterExpressionsRectorTest extends TestCase
{
    public static function families(): array
    {
        return [
            'import example' => ['import_example_runtime.php'],
            'concealment' => ['concealment_runtime.php'],
            'application-owned marker' => ['application_concealment_marker_runtime.php'],
        ];
    }

    #[DataProvider('families')]
    public function test_full_set_preserves_public_behavior_and_is_idempotent_after_legacy_reintroduction(string $name): void
    {
        $directory = sys_get_temp_dir() . '/filament-getter-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        $fixture = $directory . '/calls.php';
        copy(__DIR__ . '/Fixtures/' . $name, $fixture);
        $configuration = $directory . '/rector.php';
        $set = var_export($root . '/packages/upgrade/src/rector.php', true);
        file_put_contents($configuration, <<<PHP
            <?php
            use Rector\Config\RectorConfig;
            return RectorConfig::configure()
                ->withPaths([__DIR__ . '/calls.php'])
                ->withCache(__DIR__ . '/cache')
                ->withSets([{$set}]);
            PHP);

        try {
            $legacy = $this->runtime($root, $fixture, true);
            $migrated = null;

            for ($round = 0; $round < 2; $round++) {
                // Reintroduce the original legacy source after the first stable migration.
                copy(__DIR__ . '/Fixtures/' . $name, $fixture);

                for ($pass = 0; $pass < 3; $pass++) {
                    $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $configuration, '--clear-cache', '--no-progress-bar', ...($pass ? ['--dry-run'] : [])], $root);
                    $process->setTimeout(120);
                    $process->run();
                    $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
                    $this->assertSame($legacy, $this->runtime($root, $fixture, false));
                    $migrated ??= file_get_contents($fixture);
                    $this->assertSame($migrated, file_get_contents($fixture));
                }
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function test_keeps_application_identification_contracts_and_unknown_dispatch_for_manual_migration(): void
    {
        $directory = sys_get_temp_dir() . '/filament-marker-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        copy(__DIR__ . '/Fixtures/concealment_marker_contracts.php.inc', $directory . '/calls.php');
        file_put_contents($directory . '/rector.php', '<?php return \\Rector\\Config\\RectorConfig::configure()->withPaths([__DIR__ . "/calls.php"])->withRules([\\Filament\\Upgrade\\Rector\\RemoveObsoleteConcealmentRector::class, \\Filament\\Upgrade\\Rector\\ReplaceImportColumnGetExampleRector::class]);');

        try {
            $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar', '--dry-run'], $root);
            $process->setTimeout(120);
            $process->run();
            $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function test_preserves_concealment_marker_identity_across_files(): void
    {
        $directory = sys_get_temp_dir() . '/filament-marker-contract-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        file_put_contents($directory . '/producer.php', <<<'PHP'
            <?php
            class CustomContainer extends \Filament\Schemas\Components\Component implements \Filament\Schemas\Components\Contracts\CanConcealComponents {}
            PHP);
        file_put_contents($directory . '/consumer.php', <<<'PHP'
            <?php
            require __DIR__ . '/producer.php';
            function identify(object $component): bool {
                return $component instanceof \Filament\Schemas\Components\Contracts\CanConcealComponents;
            }
            function typedIdentity(\Filament\Schemas\Components\Contracts\CanConcealComponents $component): object {
                return $component;
            }
            $component = new CustomContainer;
            echo json_encode([identify($component), typedIdentity($component) === $component]);
            PHP);
        $set = var_export($root . '/packages/upgrade/src/rector.php', true);
        file_put_contents($directory . '/rector.php', '<?php return \\Rector\\Config\\RectorConfig::configure()->withPaths([__DIR__ . "/producer.php", __DIR__ . "/consumer.php"])->withCache(__DIR__ . "/cache")->withSets([' . $set . ']);');

        try {
            $legacy = $this->runtime($root, $directory . '/consumer.php', true);
            $this->assertSame('[true,true]', $legacy);
            $upgrade = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar'], $root);
            $upgrade->setTimeout(120);
            $upgrade->mustRun();
            $this->assertSame($legacy, $this->runtime($root, $directory . '/consumer.php', false));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    private function runtime(string $root, string $fixture, bool $legacy): string
    {
        $bootstrap = __DIR__ . '/Fixtures/removed_getters_legacy_bootstrap.php';
        $process = new Process([PHP_BINARY, '-r', 'require $argv[1]; if ($argv[3]) { require $argv[3]; } require $argv[2];', $root . '/vendor/autoload.php', $fixture, $legacy ? $bootstrap : ''], $root);
        $process->run();
        $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput() . file_get_contents($fixture));

        return $process->getOutput();
    }
}
