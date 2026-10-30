<?php

namespace Filament\Tests\Upgrade;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class RenamePlainSchemaAliasesRectorTest extends TestCase
{
    public function test_product_traits_do_not_reintroduce_plain_schema_aliases(): void
    {
        foreach (['cacheForm', 'hasCachedForm', 'getForm', 'getCachedForms', 'isCachingForms', 'getActiveFormsLocale', 'getOldFormState'] as $method) {
            $this->assertFalse(method_exists(InteractsWithForms::class, $method), $method);
        }

        foreach (['cacheInfolist', 'hasCachedInfolist', 'getInfolist', 'getCachedInfolists'] as $method) {
            $this->assertFalse(method_exists(InteractsWithInfolists::class, $method), $method);
        }
    }

    #[DataProvider('strictness')]
    public function test_preserves_legacy_public_behavior_without_product_aliases_and_is_idempotent(bool $strict): void
    {
        $root = dirname(__DIR__, 3);
        $directory = sys_get_temp_dir() . '/filament-schema-aliases-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $fixture = $directory . '/calls.php';
        copy(__DIR__ . '/Fixtures/PlainSchemaAliases/calls.php', $fixture);

        if ($strict) {
            file_put_contents($fixture, str_replace('<?php', '<?php declare(strict_types=1);', file_get_contents($fixture)));
        }

        $bootstrap = __DIR__ . '/Fixtures/PlainSchemaAliases/legacy.php';
        $configuration = $directory . '/rector.php';
        $set = var_export($root . '/packages/upgrade/src/rector.php', true);

        file_put_contents($configuration, <<<PHP
            <?php
            use Rector\Config\RectorConfig;
            return RectorConfig::configure()
                ->withPaths([__DIR__ . '/calls.php'])
                ->withCache(__DIR__ . '/cache')
                ->withoutParallel()
                ->withSets([{$set}])
                ->withRules([\Filament\Upgrade\Rector\RenamePlainSchemaAliasesRector::class]);
            PHP);

        try {
            $legacy = new Process([PHP_BINARY, '-r', 'require $argv[1]; require $argv[2]; require $argv[3];', $root . '/vendor/autoload.php', $bootstrap, $fixture], $root, ['FILAMENT_LEGACY_SCHEMA_ALIASES' => '1']);
            $legacy->mustRun();
            $this->assertStringStartsWith('PASS:', $legacy->getOutput());
            $this->assertSame('', $legacy->getErrorOutput());

            $original = file_get_contents($fixture);
            $migrated = null;

            for ($pass = 0; $pass < 5; $pass++) {
                if ($pass === 3) {
                    file_put_contents($fixture, $original);
                }

                $process = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $configuration, '--clear-cache', '--no-progress-bar', ...(($pass % 3) ? ['--dry-run'] : [])], $root);
                $process->setTimeout(120);
                $process->run();
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
                $migrated ??= file_get_contents($fixture);
                $this->assertSame($migrated, file_get_contents($fixture));

                $runtime = new Process([PHP_BINARY, '-r', 'require $argv[1]; require $argv[2]; require $argv[3];', $root . '/vendor/autoload.php', $bootstrap, $fixture], $root, ['FILAMENT_LEGACY_SCHEMA_ALIASES' => '0']);
                $runtime->run();
                $this->assertSame(0, $runtime->getExitCode(), $runtime->getOutput() . $runtime->getErrorOutput() . file_get_contents($fixture));
                $this->assertSame('', $runtime->getErrorOutput());
                $this->assertSame($legacy->getOutput(), $runtime->getOutput());
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public static function strictness(): array
    {
        return [[false], [true]];
    }
}
