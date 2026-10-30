<?php

namespace Filament\Tests\Upgrade;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tests\Actions\TestCase;
use Illuminate\Filesystem\Filesystem;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use ReflectionProperty;
use Symfony\Component\Process\Process;

class ActionTestingMigrationTest extends TestCase
{
    public function test_migrates_public_action_testing_behavior_without_legacy_macros_and_is_fresh_process_idempotent(): void
    {
        $directory = sys_get_temp_dir() . '/filament-action-testing-' . bin2hex(random_bytes(8));
        mkdir($directory);
        mkdir($directory . '/app');
        $root = dirname(__DIR__, 3);
        $source = file_get_contents(__DIR__ . '/Fixtures/ActionTesting/macros.php.inc');
        $macros = new ReflectionProperty(Testable::class, 'macros');
        $originalMacros = $macros->getValue();
        $methods = ['setActionData', 'assertActionDataSet', 'assertHasActionErrors', 'assertHasNoActionErrors'];

        try {
            foreach ($methods as $method) {
                $this->assertFalse(method_exists(TestsActions::class, $method));
                $this->assertFalse(Testable::hasMacro($method));
            }

            file_put_contents($directory . '/app/macros.php', $source);
            $expectedFiles = [];
            foreach (glob(__DIR__ . '/Fixtures/deprecated_testing_assertions*.php.inc') as $fixture) {
                $parts = explode("-----\n", file_get_contents($fixture), 2);
                $path = $directory . '/app/' . basename($fixture, '.inc');
                file_put_contents($path, $parts[0]);
                $expectedFiles[$path] = trim($parts[1] ?? $parts[0]);
            }
            file_put_contents($directory . '/rector.php', '<?php return \\Rector\\Config\\RectorConfig::configure()'
                . '->withPaths([__DIR__ . "/app"])'
                . '->withoutParallel()'
                . '->withCache(__DIR__ . "/cache")'
                . '->withSets([' . var_export($root . '/packages/upgrade/src/rector.php', true) . ']);');
            $run = function (bool $dryRun = false) use ($directory, $root): void {
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php',
                    '--clear-cache', '--no-progress-bar', ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(180);
                $process->run();
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            };
            $run();
            foreach ($expectedFiles as $path => $expected) {
                $this->assertSame($expected, trim(file_get_contents($path)), basename($path));
            }
            $migrated = file_get_contents($directory . '/app/macros.php');
            foreach ($methods as $method) {
                $this->assertStringNotContainsString('->' . $method . '(', $migrated);
            }
            $run(dryRun: true);
            $this->assertSame($migrated, file_get_contents($directory . '/app/macros.php'));
            file_put_contents($directory . '/app/macros.php', $source);
            $run();
            $this->assertSame($migrated, file_get_contents($directory . '/app/macros.php'));

            require_once __DIR__ . '/Fixtures/ActionTesting/legacy.php.inc';
            $legacySource = str_replace('Fixtures\\ActionTesting', 'Fixtures\\LegacyActionTesting', $source);
            file_put_contents($directory . '/legacy.php', $legacySource);
            require $directory . '/legacy.php';
            require $directory . '/app/macros.php';

            // Compare real mounted schemas, validation, callbacks, and returns.
            Testable::mixin(new LegacyActionTestingCallbacks);
            $legacy = $this->exercisePublicBehavior('LegacyActionTesting');
            $macros->setValue(null, $originalMacros);
            $actual = $this->exercisePublicBehavior('ActionTesting');
            $this->assertSame($legacy, $actual);
        } finally {
            $macros->setValue(null, $originalMacros);
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    private function exercisePublicBehavior(string $fixture): array
    {
        $namespace = 'Filament\\Tests\\Upgrade\\Fixtures\\' . $fixture;
        $testable = Livewire::test(ActionTestingMigrationComponent::class)->mountAction('edit');
        $results = ($namespace . '\\exercise')($testable);
        foreach ($results as $index => $result) {
            $this->assertSame($index === 3 ? null : $testable, $result);
        }
        $testable->assertSchemaStateSet(['title' => 'Changed']);
        $testable->fillForm(['title' => null])->callMountedAction();
        $this->assertSame($testable, ($namespace . '\\errors')($testable));

        return [$testable->get('mountedActions'), $testable->errors()->messages()];
    }
}

class ActionTestingMigrationComponent extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function editAction(): Action
    {
        return Action::make('edit')
            ->schema([TextInput::make('title')->required()])
            ->action(static function (): void {});
    }

    public function render(): string
    {
        return '<div></div>';
    }
}
