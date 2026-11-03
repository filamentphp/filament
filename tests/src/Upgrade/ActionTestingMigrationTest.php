<?php

namespace Filament\Tests\Upgrade;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Testing\TestsFormComponentActions;
use Filament\Infolists\Testing\TestsInfolistActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
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
        $compatibilityMethods = [];
        foreach ([
            'FormComponentAction' => TestsFormComponentActions::class,
            'InfolistAction' => TestsInfolistActions::class,
            'TableAction' => TestsTableActions::class,
            'TableBulkAction' => TestsBulkActions::class,
        ] as $prefix => $owner) {
            foreach ([
                "set{$prefix}Data" => 'setActionData',
                "assert{$prefix}DataSet" => 'assertActionDataSet',
                "assertHas{$prefix}Errors" => 'assertHasActionErrors',
                "assertHasNo{$prefix}Errors" => 'assertHasNoActionErrors',
            ] as $method => $delegate) {
                $this->assertFalse(method_exists($owner, $method));
                $this->assertFalse(Testable::hasMacro($method));
                $compatibilityMethods[$method] = $delegate;
            }
        }
        $methods = [...$methods, ...array_keys($compatibilityMethods)];

        try {
            foreach ($methods as $method) {
                $this->assertFalse(Testable::hasMacro($method));
            }
            $this->assertFalse(method_exists(TestsActions::class, 'setActionData'));

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
            $legacyCallbacks = new LegacyActionTestingCallbacks;
            Testable::mixin($legacyCallbacks);
            foreach ($compatibilityMethods as $method => $delegate) {
                Testable::macro($method, $legacyCallbacks->{$delegate}());
            }
            $legacy = [
                $this->exercisePublicBehavior('LegacyActionTesting'),
                $this->exercisePublicBehavior('LegacyActionTesting', isMounted: false),
            ];
            $macros->setValue(null, $originalMacros);
            $actual = [
                $this->exercisePublicBehavior('ActionTesting'),
                $this->exercisePublicBehavior('ActionTesting', isMounted: false),
            ];
            $this->assertSame($legacy, $actual);
        } finally {
            $macros->setValue(null, $originalMacros);
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    private function exercisePublicBehavior(string $fixture, bool $isMounted = true): array
    {
        $namespace = 'Filament\\Tests\\Upgrade\\Fixtures\\' . $fixture;
        $testable = Livewire::test(ActionTestingMigrationComponent::class);
        if ($isMounted) {
            $testable->mountAction('edit');
        }
        $results = ($namespace . '\\exercise')($testable);
        foreach ($results as $index => $result) {
            $this->assertSame($index === 3 ? null : $testable, $result);
        }
        foreach (($namespace . '\\compatibilityAliases')($testable) as $index => $result) {
            $this->assertSame($index === 8 ? null : $testable, $result);
        }
        $testable->assertSchemaStateSet(['title' => 'Changed']);
        $testable->fillForm(['title' => null]);
        if ($isMounted) {
            $testable->callMountedAction();
        } else {
            $testable->call('validateForm');
        }
        $this->assertSame($testable, ($namespace . '\\errors')($testable));
        $this->assertSame($testable, ($namespace . '\\compatibilityErrors')($testable));

        return [$testable->get('mountedActions'), $testable->errors()->messages()];
    }
}

class ActionTestingMigrationComponent extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $data = [];

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([TextInput::make('title')->required()]);
    }

    public function getDefaultTestingSchemaName(): ?string
    {
        return 'form';
    }

    public function validateForm(): void
    {
        $this->form->validate();
    }

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
