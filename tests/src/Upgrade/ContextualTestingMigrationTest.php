<?php

namespace Filament\Tests\Upgrade;

use Closure;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Testing\TestsFormComponentActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Infolists\Testing\TestsInfolistActions;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
use Filament\Tests\Fixtures\Livewire\InfolistActions;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Filament\Tests\Upgrade\Fixtures\ContextualTesting\ContextualForms;
use Filament\Tests\Upgrade\Fixtures\ContextualTesting\ContextualTable;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\AssertionFailedError;
use ReflectionProperty;
use Symfony\Component\Process\Process;

class ContextualTestingMigrationTest extends TestCase
{
    public function test_migrates_all_77_contextual_assertions_with_historical_signatures_and_real_livewire_behavior(): void
    {
        require_once __DIR__ . '/Fixtures/ContextualTesting/components.php.inc';
        $this->actingAs(User::factory()->create());
        $historical = require __DIR__ . '/Fixtures/ContextualTesting/legacy.php.inc';
        $this->assertCount(77, $historical);
        $macros = new ReflectionProperty(Testable::class, 'macros');
        $originalMacros = $macros->getValue();
        $directory = sys_get_temp_dir() . '/filament-contextual-testing-' . bin2hex(random_bytes(8));
        mkdir($directory);
        mkdir($directory . '/app');

        try {
            $owners = [TestsForms::class, TestsFormComponentActions::class, TestsInfolistActions::class, TestsTableActions::class, TestsBulkActions::class];
            foreach (array_keys($historical) as $method) {
                $this->assertFalse(Testable::hasMacro($method), $method);
                foreach ($owners as $owner) {
                    $this->assertFalse(method_exists($owner, $method), "$owner::$method");
                }
                foreach (['forms', 'infolists', 'tables'] as $package) {
                    $this->assertDoesNotMatchRegularExpression('/function\s+' . $method . '\s*\(/', file_get_contents(dirname(__DIR__, 3) . "/packages/$package/.stubs.php"));
                }
            }

            $inventory = $this->inventorySource(array_keys($historical));
            $syntax = file_get_contents(__DIR__ . '/Fixtures/ContextualTesting/syntax.php.inc');
            file_put_contents($directory . '/app/inventory.php', $inventory);
            file_put_contents($directory . '/app/syntax.php', $syntax);
            $root = dirname(__DIR__, 3);
            file_put_contents($directory . '/rector.php', '<?php return \\Rector\\Config\\RectorConfig::configure()'
                . '->withPaths([__DIR__ . "/app"])->withoutParallel()->withCache(__DIR__ . "/cache")'
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
            $migrated = file_get_contents($directory . '/app/inventory.php');
            $migratedSyntax = file_get_contents($directory . '/app/syntax.php');
            $this->assertExactHistoricalAdapters($migrated);
            foreach (array_keys($historical) as $method) {
                $this->assertDoesNotMatchRegularExpression('/(?:->|\?->)' . $method . '\s*\(/', $migrated . $migratedSyntax);
            }
            $run(dryRun: true);
            $this->assertSame($migrated, file_get_contents($directory . '/app/inventory.php'));
            $this->assertSame($migratedSyntax, file_get_contents($directory . '/app/syntax.php'));
            file_put_contents($directory . '/app/inventory.php', $inventory);
            file_put_contents($directory . '/app/syntax.php', $syntax);
            $run();
            $this->assertSame($migrated, file_get_contents($directory . '/app/inventory.php'));
            $this->assertSame($migratedSyntax, file_get_contents($directory . '/app/syntax.php'));
            require $directory . '/app/inventory.php';
            require $directory . '/app/syntax.php';
            file_put_contents($directory . '/legacy-syntax.php', str_replace('ContextualTesting\\Current', 'ContextualTesting\\Historical', $syntax));
            require $directory . '/legacy-syntax.php';

            foreach ($historical as $method => $callback) {
                Testable::macro($method, $callback);
            }
            $legacyAdapters = static fn (Testable $testable): array => array_map(
                static fn (Closure $callback): Closure => Closure::bind($callback, $testable, Testable::class),
                $historical,
            );
            $legacy = $this->exerciseContracts($legacyAdapters, 'Historical');
            $macros->setValue(null, $originalMacros);
            $actual = $this->exerciseContracts('Filament\\Tests\\Upgrade\\Fixtures\\ContextualTesting\\Current\\adapters', 'Current');
            $this->assertSame($legacy, $actual);
        } finally {
            $macros->setValue(null, $originalMacros);
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    private function inventorySource(array $methods): string
    {
        $items = array_map(static fn (string $method): string => "        '$method' => \$testable->$method(...),", $methods);

        return "<?php\nnamespace Filament\\Tests\\Upgrade\\Fixtures\\ContextualTesting\\Current;\nuse Livewire\\Features\\SupportTesting\\Testable;\nfunction adapters(Testable \$testable): array\n{\n    return [\n" . implode("\n", $items) . "\n    ];\n}\n";
    }

    private function assertExactHistoricalAdapters(string $migrated): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $finder = new NodeFinder;
        $printer = new Standard;
        $normalize = static function (string $source) use ($parser): array {
            $nodes = (new NodeTraverser(new NameResolver))->traverse($parser->parse($source));

            return (new NodeTraverser(new class extends NodeVisitorAbstract
            {
                public function enterNode(Node $node): void
                {
                    $node->setAttributes([]);
                    if (($node instanceof Node\Expr\ConstFetch) && in_array(strtolower($node->name->toString()), ['null', 'true', 'false'], true)) {
                        $node->name = new Node\Name(strtolower($node->name->toString()));
                    }
                }
            }))->traverse($nodes);
        };
        $historical = $finder->findFirstInstanceOf($normalize(file_get_contents(__DIR__ . '/Fixtures/ContextualTesting/legacy.php.inc')), Node\Expr\Array_::class);
        $actual = $finder->findFirstInstanceOf($normalize($migrated), Node\Expr\Array_::class);
        $this->assertCount(77, $actual->items);
        foreach ($historical->items as $index => $item) {
            $binding = $finder->findFirstInstanceOf([$actual->items[$index]->value], Node\Expr\StaticCall::class);
            $this->assertSame($item->key->value, $actual->items[$index]->key->value);
            $this->assertInstanceOf(Node\Expr\StaticCall::class, $binding, $item->key->value);
            $this->assertSame('Closure', $binding->class->toString());
            $this->assertSame('bind', $binding->name->name);
            $this->assertSame('$testable', $printer->prettyPrintExpr($binding->args[1]->value));
            $this->assertSame(Testable::class, $binding->args[2]->value->value);
            // Compare every signature and statement against independent historical source,
            // not output copied from the rule. Normalize imports, comments, and formatting.
            $this->assertSame($printer->prettyPrintExpr($item->value), $printer->prettyPrintExpr($binding->args[0]->value), $item->key->value);
        }
    }

    private function exerciseContracts(callable $adapters, string $syntaxNamespace): array
    {
        $outcomes = [];
        foreach ([
            'FormComponent' => [ContextualForms::class, 'textInput'],
            'Infolist' => [InfolistActions::class, 'textEntry'],
            'Table' => [PostsTable::class, null],
            'TableBulk' => [PostsTable::class, null],
        ] as $family => [$component, $schemaComponent]) {
            $testable = Livewire::test($component);
            $callbacks = $adapters($testable);
            $prefix = "assert{$family}Action";
            $context = match ($family) {
                'FormComponent' => ['components' => $schemaComponent],
                'Infolist' => ['component' => $schemaComponent],
                default => [],
            };
            $cases = [
                'Exists' => ['exists'],
                'DoesNotExist' => ['missing'],
                'Visible' => ['visible'],
                'Hidden' => ['hidden'],
                'Enabled' => ['enabled'],
                'Disabled' => ['disabled'],
                'HasIcon' => ['hasIcon', 'icon' => Heroicon::PencilSquare],
                'DoesNotHaveIcon' => ['hasIcon', 'icon' => Heroicon::Trash],
                'HasLabel' => ['hasLabel', 'label' => 'My Action'],
                'DoesNotHaveLabel' => ['hasLabel', 'label' => 'Not my label'],
                'HasColor' => ['hasColor', 'color' => 'primary'],
                'DoesNotHaveColor' => ['hasColor', 'color' => 'danger'],
            ];
            if ($family !== 'TableBulk') {
                $cases += [
                    'HasUrl' => ['url', 'url' => 'https://filamentphp.com'],
                    'DoesNotHaveUrl' => ['url', 'url' => 'https://example.com'],
                    'ShouldOpenUrlInNewTab' => ['urlInNewTab'],
                    'ShouldNotOpenUrlInNewTab' => ['urlNotInNewTab'],
                ];
            }
            foreach ($cases as $suffix => $arguments) {
                $action = array_shift($arguments);
                $this->assertSame($testable, $callbacks[$prefix . $suffix](...['actions' => $action, ...$context, ...$arguments]));
            }
            foreach (['Visible' => 'hidden', 'Enabled' => 'disabled', 'HasLabel' => 'hasLabel'] as $suffix => $action) {
                $arguments = ['actions' => $action, ...$context, ...($suffix === 'HasLabel' ? ['label' => 'Wrong label'] : [])];
                $outcomes[$family . $suffix] = $this->assertionFailure(static fn () => $callbacks[$prefix . $suffix](...$arguments));
            }
            $action = TestAction::make('halt');
            $action = match ($family) {
                'FormComponent' => $action->schemaComponent('textInput', 'form'),
                'Infolist' => $action->schemaComponent('textEntry', 'infolist'),
                'Table' => $action->table(),
                'TableBulk' => $action->table()->bulk(),
            };
            $this->assertSame($testable, $callbacks[$prefix . 'NotMounted'](...['actions' => 'halt', ...$context]));
            if ($family === 'TableBulk') {
                $testable->selectTableRecords([Post::factory()->create()]);
            }
            $testable->mountAction($action);
            // `Halted` historically means mounted, even before calling the action.
            foreach (['Mounted', 'Halted'] as $suffix) {
                $this->assertSame($testable, $callbacks[$prefix . $suffix](...['actions' => 'halt', ...$context]));
            }
            $testable->callMountedAction();
            $this->assertSame($testable, $callbacks[$prefix . 'Halted'](...['actions' => 'halt', ...$context]));
            $testable->unmountAction();
            $this->assertSame($testable, $callbacks[$prefix . 'NotMounted'](...['actions' => 'halt', ...$context]));
        }

        $forms = Livewire::test(ContextualForms::class);
        $callbacks = $adapters($forms);
        foreach ([false, true] as $mounted) {
            if ($mounted) {
                $forms->mountAction(TestAction::make('setValue')->schemaComponent('textInput', 'form'));
            }
            $this->assertSame($forms, $callbacks['assertFormExists']());
            $this->assertSame($forms, $callbacks['assertFormSet'](state: ['textInput' => null]));
            $this->assertSame($forms, $callbacks['assertFormSet'](static fn (array $state): bool => array_key_exists('textInput', $state)));
            $this->assertSame($forms, $callbacks['assertFormComponentExists']('textInput', static fn ($component): bool => $component->getName() === 'textInput'));
            $this->assertSame($forms, $callbacks['assertFormComponentDoesNotExist']('different'));
            $this->assertSame($forms, $callbacks['assertFormComponentExists'](componentKey: 'different', form: 'other'));
            $outcomes['schema' . (int) $mounted] = $this->assertionFailure(static fn () => $callbacks['assertFormComponentExists']('different'));
        }
        $forms->unmountAction();
        $forms->mountAction(TestAction::make('outer')->schemaComponent('parent', 'form'));
        $this->assertSame($forms, $callbacks['assertFormComponentActionHasLabel'](
            components: ['parent', $forms->instance()->getMountedActionSchemaName() . '.child'],
            actions: ['outer', 'inner'],
            label: 'Nested action',
        ));
        $forms->unmountAction();
        $syntax = 'Filament\\Tests\\Upgrade\\Fixtures\\ContextualTesting\\' . $syntaxNamespace . '\\syntax';
        [$results, $receiverCalls, $argumentCalls] = $syntax($forms);
        foreach ($results as $index => $result) {
            $this->assertSame($index === 3 ? null : $forms, $result);
        }
        $this->assertSame(1, $receiverCalls);
        $this->assertSame(2, $argumentCalls);
        $shortCircuit = 'Filament\\Tests\\Upgrade\\Fixtures\\ContextualTesting\\' . $syntaxNamespace . '\\shortCircuit';
        $this->assertSame([null, null], $shortCircuit(null));
        $weakCallback = 'Filament\\Tests\\Upgrade\\Fixtures\\ContextualTesting\\' . $syntaxNamespace . '\\weakCallback';
        $this->assertSame($forms, $weakCallback($forms));

        $table = Livewire::test(ContextualTable::class);
        $callbacks = $adapters($table);
        $record = Post::factory()->create();
        $tableSyntax = 'Filament\\Tests\\Upgrade\\Fixtures\\ContextualTesting\\' . $syntaxNamespace . '\\tableSyntax';
        $this->assertSame($table, $tableSyntax($table, $record));
        $this->assertSame($table, $callbacks['assertTableActionExists']('edit', record: (string) $record->getKey()));
        $this->assertSame($table, $callbacks['assertTableActionExists'](
            actions: [['name' => 'parent'], ['name' => 'footer']],
            checkActionUsing: static fn ($action): bool => $action->getRecord()->is($record),
            record: $record,
        ));
        $this->assertSame($table, $callbacks['assertTableBulkActionsExistInOrder'](['exists', 'existsInOrder', 'lastBulkAction']));
        $outcomes['bulkOrder'] = $this->assertionFailure(static fn () => $callbacks['assertTableBulkActionsExistInOrder'](['existsInOrder', 'exists']));
        $outcomes['bulkType'] = $this->assertionFailure(static fn () => $callbacks['assertTableBulkActionsExistInOrder'](['ordinaryToolbarAction']));

        return $outcomes;
    }

    private function assertionFailure(callable $assertion): string
    {
        try {
            $assertion();
        } catch (AssertionFailedError $exception) {
            return $exception->getMessage();
        }

        $this->fail('The deliberately incorrect assertion unexpectedly passed.');
    }
}
