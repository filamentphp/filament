<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenamePlainSchemaAliasesRectorTest extends AbstractRectorTestCase
{
    public function test_renames_typed_schema_calls_and_preserves_explicit_cache_arguments(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/plain_schema_aliases.php.inc');
    }

    public function test_explicit_null_removes_the_cached_schema_without_building_it(): void
    {
        $directory = sys_get_temp_dir() . '/filament-schema-aliases-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        copy(__DIR__ . '/Fixtures/plain_schema_runtime.php', $directory . '/calls.php');

        try {
            $upgrade = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/calls.php', '--config', $this->provideConfigFilePath(), '--clear-cache', '--no-progress-bar'], $root);
            $upgrade->setTimeout(120);
            $upgrade->mustRun();
            $runtime = new Process([PHP_BINARY, '-r', 'require $argv[1]; require $argv[2];', $root . '/vendor/autoload.php', $directory . '/calls.php'], $root);
            $runtime->mustRun();
            $this->assertSame("PASS\n", $runtime->getOutput());
            $this->assertSame('', $runtime->getErrorOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/plain_schema_aliases_config.php';
    }
}
