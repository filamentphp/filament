<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\BulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsFormComponentActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Infolists\Testing\TestsInfolistActions;
use Filament\Schemas\Testing\TestsSchemas;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
use Filament\Tables\Testing\TestsColumns;
use Filament\Tables\Testing\TestsFilters;
use Filament\Tables\Testing\TestsRecords;
use Filament\Tables\Testing\TestsSummaries;
use Livewire\Features\SupportTesting\Testable;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class RenameDeprecatedTestingAssertionsRector extends AbstractRector
{
    private const FLUENT_TESTABLE_METHODS = ['set', 'call', 'assertDispatched', 'assertNotDispatched', 'assertDispatchedTo'];

    /** @var array<string, array{string, class-string}> */
    private const ASSERTIONS = [
        'setActionData' => ['fillForm', Testable::class],
        'assertActionDataSet' => ['assertSchemaStateSet', Testable::class],
        'assertHasActionErrors' => ['assertHasFormErrors', Testable::class],
        'assertHasNoActionErrors' => ['assertHasNoFormErrors', Testable::class],
        'setFormComponentActionData' => ['fillForm', Testable::class],
        'assertFormComponentActionDataSet' => ['assertSchemaStateSet', Testable::class],
        'assertHasFormComponentActionErrors' => ['assertHasFormErrors', Testable::class],
        'assertHasNoFormComponentActionErrors' => ['assertHasNoFormErrors', Testable::class],
        'setInfolistActionData' => ['fillForm', Testable::class],
        'assertInfolistActionDataSet' => ['assertSchemaStateSet', Testable::class],
        'assertHasInfolistActionErrors' => ['assertHasFormErrors', Testable::class],
        'assertHasNoInfolistActionErrors' => ['assertHasNoFormErrors', Testable::class],
        'setTableActionData' => ['fillForm', Testable::class],
        'assertTableActionDataSet' => ['assertSchemaStateSet', Testable::class],
        'assertHasTableActionErrors' => ['assertHasFormErrors', Testable::class],
        'assertHasNoTableActionErrors' => ['assertHasNoFormErrors', Testable::class],
        'setTableBulkActionData' => ['fillForm', Testable::class],
        'assertTableBulkActionDataSet' => ['assertSchemaStateSet', Testable::class],
        'assertHasTableBulkActionErrors' => ['assertHasFormErrors', Testable::class],
        'assertHasNoTableBulkActionErrors' => ['assertHasNoFormErrors', Testable::class],
        'assertFormFieldIsDisabled' => ['assertFormFieldDisabled', TestsForms::class],
        'assertFormFieldIsEnabled' => ['assertFormFieldEnabled', TestsForms::class],
        'assertFormFieldIsReadOnly' => ['assertFormFieldReadOnly', TestsForms::class],
        'assertFormFieldIsHidden' => ['assertFormFieldHidden', TestsForms::class],
        'assertFormFieldIsVisible' => ['assertFormFieldVisible', TestsForms::class],
        'assertActionHeld' => ['assertActionHalted', TestsActions::class],
        'assertTableActionHeld' => ['assertTableActionHalted', TestsTableActions::class],
        'assertTableActionsExistInOrder' => ['assertTableRecordActionsExistInOrder', TestsTableActions::class],
        'assertTableBulkActionHeld' => ['assertTableBulkActionHalted', TestsBulkActions::class],
    ];

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /** @param MethodCall | NullsafeMethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $contextual = $this->getContextualAssertion($node->name->name);

        if ((! $contextual) && (! isset(self::ASSERTIONS[$node->name->name]))) {
            return null;
        }

        [$replacement, $mixin] = $contextual ? ['', Testable::class] : self::ASSERTIONS[$node->name->name];
        $testableType = $this->getTestableType($node->var);

        if ((! $testableType) && (! $this->isObjectType($node->var, new ObjectType($mixin)))) {
            return null;
        }

        if ($mixin === Testable::class) {
            foreach ($testableType?->getObjectClassReflections() ?? [] as $class) {
                if ($class->hasNativeMethod($node->name->name)) {
                    return null;
                }
            }
        }

        if ($contextual) {
            return $this->migrateContextualAssertion($node, ...$contextual);
        }

        if (in_array($replacement, ['fillForm', 'assertSchemaStateSet'], strict: true)) {
            foreach ($node->isFirstClassCallable() ? [] : $node->getArgs() as $argument) {
                if ($argument->unpack && (! $argument->value instanceof Array_)) {
                    return null;
                }
            }

            foreach ($node->isFirstClassCallable() ? [] : $node->getArgs() as $argument) {
                if ($argument->name?->name === 'data') {
                    $argument->name = new Identifier('state');
                }

                if ($argument->unpack && $argument->value instanceof Array_) {
                    foreach ($argument->value->items as $item) {
                        if ($item->key instanceof String_ && ($item->key->value === 'data')) {
                            $item->key = new String_('state');
                        }
                    }
                }
            }
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    private function getTestableType(Expr $receiver): ?Type
    {
        $type = TypeCombinator::removeNull($this->getType($receiver));

        if (($receiver instanceof MethodCall) || ($receiver instanceof NullsafeMethodCall)) {
            if ((! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
                return null;
            }

            if ($ownerType = $this->getTestableType($receiver->var)) {
                $method = $receiver->name->name;

                foreach ($ownerType->getObjectClassReflections() as $class) {
                    if ($class->hasNativeMethod($method)) {
                        if (($class->getNativeMethod($method)->getDeclaringClass()->getName() === Testable::class)
                            && in_array($method, self::FLUENT_TESTABLE_METHODS, strict: true)) {
                            return $ownerType;
                        }

                        return (new ObjectType(Testable::class))->isSuperTypeOf($type)->yes() ? $type : null;
                    }
                }

                if (isset(self::ASSERTIONS[$method]) || $this->getContextualAssertion($method) || in_array($method, array_column(self::ASSERTIONS, 0), strict: true)) {
                    return $ownerType;
                }

                // Registered testing factories return the same `Testable`,
                // except the parsers that return nested action arrays.
                if (! str_starts_with($method, 'parseNested')) {
                    foreach ([TestsActions::class, TestsFormComponentActions::class, TestsForms::class, TestsInfolistActions::class, TestsSchemas::class, TestsTableActions::class, TestsBulkActions::class, TestsColumns::class, TestsFilters::class, TestsRecords::class, TestsSummaries::class] as $testingMixin) {
                        if (class_exists($testingMixin) && in_array($method, get_class_methods($testingMixin), strict: true)) {
                            return $ownerType;
                        }
                    }
                }

                return null;
            }
        }

        return (new ObjectType(Testable::class))->isSuperTypeOf($type)->yes() ? $type : null;
    }

    /** @return array{string, list<string>, string} | null */
    private function getContextualAssertion(string $method): ?array
    {
        $schemas = [
            'assertFormSet' => ['assertSchemaStateSet', ['state', 'form'], 'schema'],
            'assertFormExists' => ['assertSchemaExists', ['name'], 'schema'],
            'assertFormComponentExists' => ['assertSchemaComponentExists', ['componentKey', 'form', 'checkComponentUsing'], 'schema'],
            'assertFormComponentDoesNotExist' => ['assertSchemaComponentDoesNotExist', ['componentKey', 'form'], 'schema'],
            'assertTableBulkActionsExistInOrder' => ['assertActionListInOrder', ['names'], 'bulkOrder'],
        ];

        if (isset($schemas[$method])) {
            return $schemas[$method];
        }

        $method = match ($method) {
            'assertTableActionHeld' => 'assertTableActionHalted',
            'assertTableBulkActionHeld' => 'assertTableBulkActionHalted',
            default => $method,
        };

        if (! preg_match('/^assert(FormComponent|Infolist|TableBulk|Table)Action(Exists|DoesNotExist|Visible|Hidden|Enabled|Disabled|HasIcon|DoesNotHaveIcon|HasLabel|DoesNotHaveLabel|HasColor|DoesNotHaveColor|HasUrl|DoesNotHaveUrl|ShouldOpenUrlInNewTab|ShouldNotOpenUrlInNewTab|Mounted|NotMounted|Halted)$/', $method, $matches)) {
            return null;
        }

        [, $family, $suffix] = $matches;

        if (($family === 'TableBulk') && str_contains($suffix, 'Url')) {
            return null;
        }

        $parameters = match ($family) {
            'FormComponent' => ['components', 'actions'],
            'Infolist' => ['component', 'actions'],
            default => ['actions'],
        };
        $value = match ($suffix) {
            'HasIcon', 'DoesNotHaveIcon' => 'icon',
            'HasLabel', 'DoesNotHaveLabel' => 'label',
            'HasColor', 'DoesNotHaveColor' => 'color',
            'HasUrl', 'DoesNotHaveUrl' => 'url',
            default => null,
        };

        if ($value) {
            $parameters[] = $value;
        }

        if (($family === 'FormComponent') && (! in_array($suffix, ['Exists', 'DoesNotExist', 'Mounted', 'NotMounted', 'Halted'], true))) {
            $parameters[] = 'arguments';
        }

        if ($family === 'FormComponent') {
            $parameters[] = 'formName';
        } elseif ($family === 'Infolist') {
            $parameters[] = 'schema';
        } elseif ($family === 'Table') {
            if (in_array($suffix, ['Exists', 'DoesNotExist'], true)) {
                $parameters[] = 'checkActionUsing';
            }

            if (! in_array($suffix, ['Mounted', 'NotMounted', 'Halted'], true)) {
                $parameters[] = 'record';
            }
        }

        return ['assertAction' . ($suffix === 'Halted' ? 'Mounted' : $suffix), $parameters, $family];
    }

    /** @param list<string> $parameters */
    private function migrateContextualAssertion(MethodCall | NullsafeMethodCall $node, string $replacement, array $parameters, string $family): ?Node
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        $arguments = [];

        foreach ($node->getArgs() as $index => $argument) {
            $name = $argument->name->name ?? $parameters[$index] ?? null;

            if ($argument->unpack || (! $name) || (! in_array($name, $parameters, true))) {
                return null;
            }

            $arguments[$name] = $argument->value;
        }

        if ($family === 'schema') {
            if ($replacement === 'assertSchemaExists') {
                $node->args = [new Arg($arguments['name'] ?? new String_('form'))];
            } else {
                $key = $arguments['state'] ?? $arguments['componentKey'] ?? null;

                if (! $key) {
                    return null;
                }

                $form = $arguments['form'] ?? new String_('form');
                $check = $arguments['checkComponentUsing'] ?? null;

                if (($replacement === 'assertSchemaComponentExists') && $this->isObjectType($form, new ObjectType(Closure::class))) {
                    $check = $form;
                    $form = new String_('form');
                } elseif (($replacement === 'assertSchemaComponentExists') && (! (new StringType)->isSuperTypeOf($this->getType($form))->yes())) {
                    return null;
                }

                $node->args = [new Arg($key), new Arg($form), ...($check ? [new Arg($check)] : [])];
            }
        } elseif ($family === 'bulkOrder') {
            if ((! isset($arguments['names'])) || (! $node->var instanceof Variable)) {
                return null;
            }

            $node->args = [
                new Arg($arguments['names']),
                new Arg(new MethodCall(new MethodCall(new MethodCall($node->var, 'instance'), 'getTable'), 'getToolbarActions')),
                new Arg(new String_('table bulk')),
                new Arg(new ClassConstFetch(new FullyQualified(BulkAction::class), 'class')),
            ];
        } else {
            $actions = $arguments['actions'] ?? null;

            if (! $actions) {
                return null;
            }

            if ($descriptors = $this->createTestActions($actions, $arguments, $family)) {
                $actions = $descriptors;
            } else {
                // Reuse the public nested-action parser for dynamic or nested lists.
                // Only repeat a plain variable, never a factory or fluent receiver.
                if (! $node->var instanceof Variable || ! is_string($node->var->name)) {
                    return null;
                }

                $parse = match ($family) {
                    'Table' => [$actions, $arguments['record'] ?? $this->nodeFactory->createNull()],
                    'TableBulk' => [$actions],
                    'FormComponent' => isset($arguments['components']) ? [$arguments['components'], $actions, $arguments['formName'] ?? new String_('form'), $arguments['arguments'] ?? new Array_] : null,
                    'Infolist' => isset($arguments['component']) ? [$arguments['component'], $actions, $arguments['schema'] ?? new String_('infolist')] : null,
                    default => null,
                };

                if (! $parse) {
                    return null;
                }

                $actions = new MethodCall($node->var, 'parseNested' . $family . 'Actions', array_map(static fn (Expr $value): Arg => new Arg($value), $parse));
            }

            $node->args = [new Arg($actions)];

            foreach (['icon', 'label', 'color', 'url', 'checkActionUsing'] as $name) {
                if (isset($arguments[$name])) {
                    $node->args[] = new Arg($arguments[$name], name: new Identifier($name));
                }
            }
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    /** @param array<string, Expr> $arguments */
    private function createTestActions(Expr $names, array $arguments, string $family): ?Expr
    {
        if ($names instanceof String_ && str_contains($names->value, '.')) {
            $names = new Array_(array_map(static fn (string $name): ArrayItem => new ArrayItem(new String_($name)), explode('.', $names->value)));
        }

        $items = $names instanceof Array_ ? $names->items : [new ArrayItem($names)];
        $actions = [];

        if (isset($arguments['arguments'])) {
            // The legacy parser also accepts arguments keyed by action name.
            return null;
        }

        foreach ($items as $index => $item) {
            if ($item->unpack || $item->key || ((! $item->value instanceof String_) && (! $item->value instanceof ClassConstFetch))) {
                return null;
            }

            $action = new StaticCall(new FullyQualified(TestAction::class), 'make', [new Arg($item->value)]);

            if (str_starts_with($family, 'Table')) {
                $record = (! $index) ? ($arguments['record'] ?? null) : null;
                $recordType = $record ? $this->getType($record) : null;
                $action = new MethodCall($action, 'table', ($record && (! $recordType->isNull()->yes()))
                    ? [new Arg($recordType->isNull()->no() ? $record : new Coalesce($record, $this->nodeFactory->createTrue()))]
                    : []);

                if ($family === 'TableBulk') {
                    $action = new MethodCall($action, 'bulk');
                }
            } else {
                $component = $arguments[$family === 'FormComponent' ? 'components' : 'component'] ?? null;

                if (! $component) {
                    return null;
                }

                if ((! $component instanceof Array_) && (! (new StringType)->isSuperTypeOf($this->getType($component))->yes())) {
                    return null;
                }

                $component = $component instanceof Array_ ? (($component->items[$index] ?? null)?->value) : ($index ? null : $component);

                if ($component) {
                    $schema = $index ? new String_('') : ($arguments[$family === 'FormComponent' ? 'formName' : 'schema'] ?? new String_($family === 'FormComponent' ? 'form' : 'infolist'));
                    $action = new MethodCall($action, 'schemaComponent', [new Arg($component), new Arg($schema)]);
                }
            }

            $actions[] = new ArrayItem($action);
        }

        return $names instanceof Array_ ? new Array_($actions) : ($actions[0]->value ?? null);
    }
}
