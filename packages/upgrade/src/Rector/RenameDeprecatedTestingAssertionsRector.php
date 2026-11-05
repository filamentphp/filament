<?php

namespace Filament\Upgrade\Rector;

use Closure;
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
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Closure as ClosureExpression;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\UnionType;
use PhpParser\ParserFactory;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class RenameDeprecatedTestingAssertionsRector extends AbstractRector
{
    private const TESTABLE_TYPE_ATTRIBUTE = 'filament_testing_assertion_type';

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

        $callback = $this->getContextualAssertion($node->name->name);

        if ((! $callback) && (! isset(self::ASSERTIONS[$node->name->name]))) {
            return null;
        }

        [$replacement, $mixin] = $callback ? ['', Testable::class] : self::ASSERTIONS[$node->name->name];
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

        if ($callback) {
            if (($node instanceof MethodCall) && $this->hasNullsafeReceiver($node->var) && (! $this->canPropagateNullsafeReceiver($node->var))) {
                return null;
            }

            $bind = new StaticCall(new FullyQualified(Closure::class), 'bind', [
                new Arg($callback),
                new Arg($node->var),
                new Arg(new String_(Testable::class)),
            ]);

            if ($node->isFirstClassCallable()) {
                return new FuncCall(new ArrowFunction([
                    'static' => true,
                    'params' => [new Param(new Variable('assertion'), type: new FullyQualified(Closure::class))],
                    'expr' => new ArrowFunction([
                        'static' => true,
                        'params' => [new Param(new Variable('arguments'), variadic: true)],
                        'expr' => new MethodCall(new Variable('assertion'), '__invoke', [new Arg(new Variable('arguments'), unpack: true)]),
                    ]),
                ]), [new Arg($bind)]);
            }

            if (($node instanceof NullsafeMethodCall) || $this->hasNullsafeReceiver($node->var)) {
                $bind->args[1] = new Arg(new Variable('testable'));
                $nullableCallback = new FuncCall(new ArrowFunction([
                    'static' => true,
                    'params' => [new Param(new Variable('testable'), type: new Node\NullableType(new FullyQualified(Testable::class)))],
                    'expr' => new Ternary(
                        new Identical(new Variable('testable'), new ConstFetch(new FullyQualified('null'))),
                        new ConstFetch(new FullyQualified('null')),
                        $bind,
                    ),
                ]), [new Arg($node->var)]);

                $invocation = new NullsafeMethodCall($nullableCallback, '__invoke', $node->args);
            } else {
                $invocation = new MethodCall($bind, '__invoke', $node->args);
            }

            $invocation->setAttribute(self::TESTABLE_TYPE_ATTRIBUTE, $testableType);

            return $invocation;
        }

        if (in_array($replacement, ['fillForm', 'assertSchemaStateSet'], strict: true)) {
            if ($node->isFirstClassCallable()) {
                return new StaticCall(new FullyQualified(Closure::class), 'bind', [
                    new Arg(new ClosureExpression([
                        'params' => [new Param(new Variable('data'), type: $replacement === 'fillForm'
                            ? new Identifier('array')
                            : new UnionType([new Identifier('array'), new FullyQualified(Closure::class)]))],
                        'returnType' => new Identifier('static'),
                        'stmts' => [
                            new Expression(new MethodCall(new Variable('this'), $replacement, [new Arg(new Variable('data'))])),
                            new Return_(new Variable('this')),
                        ],
                    ])),
                    new Arg($node->var),
                    new Arg(new String_(Testable::class)),
                ]);
            }

            foreach ($node->args as $argument) {
                if ($argument->name?->name === 'data') {
                    $argument->name = new Identifier('state');
                }

                if ($argument->unpack) {
                    $argument->value = new FuncCall(new ClosureExpression([
                        'static' => true,
                        'params' => [new Param(new Variable('arguments'), type: new Identifier('iterable'))],
                        'returnType' => new Identifier('iterable'),
                        'stmts' => [new Foreach_(new Variable('arguments'), new Variable('value'), [
                            'keyVar' => new Variable('key'),
                            'stmts' => [new Expression(new Yield_(new Variable('value'), new Ternary(
                                new Identical(new Variable('key'), new String_('data')),
                                new String_('state'),
                                new Variable('key'),
                            )))],
                        ])],
                    ]), [new Arg($argument->value)]);
                }
            }
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    private function getTestableType(Expr $receiver): ?Type
    {
        if (($generatedType = $receiver->getAttribute(self::TESTABLE_TYPE_ATTRIBUTE)) instanceof Type) {
            return $generatedType;
        }

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

    private function hasNullsafeReceiver(Expr $receiver): bool
    {
        while (($receiver instanceof MethodCall) || ($receiver instanceof Expr\PropertyFetch) || ($receiver instanceof Expr\ArrayDimFetch)
            || ($receiver instanceof NullsafeMethodCall) || ($receiver instanceof Expr\NullsafePropertyFetch)) {
            if (($receiver instanceof NullsafeMethodCall) || ($receiver instanceof Expr\NullsafePropertyFetch)) {
                return true;
            }

            $receiver = $receiver->var;
        }

        return false;
    }

    private function canPropagateNullsafeReceiver(Expr $receiver): bool
    {
        if ($receiver->getAttribute(self::TESTABLE_TYPE_ATTRIBUTE) instanceof Type) {
            return true;
        }

        if ((! (($receiver instanceof MethodCall) || ($receiver instanceof NullsafeMethodCall))) || (! $receiver->name instanceof Identifier)) {
            return false;
        }

        $ownerType = $this->getTestableType($receiver->var);

        if ((! $ownerType) || (! $this->getTestableType($receiver))) {
            return false;
        }

        foreach ($ownerType->getObjectClassReflections() as $class) {
            if (! $class->hasNativeMethod($receiver->name->name)) {
                continue;
            }

            $method = $class->getNativeMethod($receiver->name->name);

            if (($method->getDeclaringClass()->getName() === Testable::class) && in_array($receiver->name->name, self::FLUENT_TESTABLE_METHODS, strict: true)) {
                continue;
            }

            foreach ($method->getVariants() as $variant) {
                if (! $variant->getReturnType()->isSuperTypeOf(new NullType)->no()) {
                    return false;
                }
            }
        }

        return ($receiver instanceof NullsafeMethodCall) || $this->canPropagateNullsafeReceiver($receiver->var);
    }

    private function getContextualAssertion(string $method): ?ClosureExpression
    {
        $schemaAssertions = [
            'assertFormSet' => 'function (array|\\Closure $state, string $form = "form"): static { $this->assertSchemaStateSet($state, $form); return $this; }',
            'assertFormExists' => 'function (string $name = "form"): static { $this->assertSchemaExists($name); return $this; }',
            'assertFormComponentExists' => 'function (string $componentKey, string|\\Closure $form = "form", ?\\Closure $checkComponentUsing = null): static { if ($form instanceof \\Closure) { $checkComponentUsing = $form; $form = "form"; } $this->assertSchemaComponentExists($componentKey, $form, $checkComponentUsing); return $this; }',
            'assertFormComponentDoesNotExist' => 'function (string $componentKey, string $form = "form"): static { $this->assertSchemaComponentDoesNotExist($componentKey, $form); return $this; }',
            'assertTableBulkActionsExistInOrder' => 'function (array $names): static { $this->assertActionListInOrder($names, $this->instance()->getTable()->getToolbarActions(), "table bulk", \\Filament\\Actions\\BulkAction::class); return $this; }',
        ];

        $code = $schemaAssertions[$method] ?? null;

        if (! $code) {
            $method = match ($method) {
                'assertTableActionHeld' => 'assertTableActionHalted',
                'assertTableBulkActionHeld' => 'assertTableBulkActionHalted',
                default => $method,
            };

            if (! preg_match('/^assert(FormComponent|Infolist|TableBulk|Table)Action(Exists|DoesNotExist|Visible|Hidden|Enabled|Disabled|HasIcon|DoesNotHaveIcon|HasLabel|DoesNotHaveLabel|HasColor|DoesNotHaveColor|HasUrl|DoesNotHaveUrl|ShouldOpenUrlInNewTab|ShouldNotOpenUrlInNewTab|Mounted|NotMounted|Halted)$/', $method, $matches)) {
                return null;
            }

            [, $family, $suffix] = $matches;

            // Bulk actions never exposed the URL assertions.
            if (($family === 'TableBulk') && str_contains($suffix, 'Url')) {
                return null;
            }

            $value = match ($suffix) {
                'HasIcon', 'DoesNotHaveIcon' => 'string|\\BackedEnum $icon',
                'HasLabel', 'DoesNotHaveLabel' => 'string $label',
                'HasColor', 'DoesNotHaveColor' => 'string|array $color',
                'HasUrl', 'DoesNotHaveUrl' => 'string $url',
                default => null,
            };
            $assertionArguments = '$actions';

            if ($value) {
                $assertionArguments .= ', $' . substr($value, strrpos($value, '$') + 1);
            }

            $simple = in_array($suffix, ['Exists', 'DoesNotExist', 'Mounted', 'NotMounted', 'Halted'], strict: true);
            $parameters = [];

            if ($family === 'FormComponent') {
                $parameters = ['string|array $components', 'string|array $actions'];
                if ($value) {
                    $parameters[] = $value;
                }
                if (! $simple) {
                    $parameters[] = 'array $arguments = []';
                }
                $parameters[] = 'string $formName = "form"';
                $parse = '$this->parseNestedFormComponentActions($components, $actions, $formName' . ($simple ? '' : ', $arguments') . ')';
            } elseif ($family === 'Infolist') {
                $parameters = ['string $component', 'string|array $actions'];
                if ($value) {
                    $parameters[] = $value;
                }
                $parameters[] = 'string $schema = "infolist"';
                $parse = '$this->parseNestedInfolistActions($component, $actions, $schema)';
            } else {
                $parameters[] = 'string|array $actions';
                if ($value) {
                    $parameters[] = $value;
                }
                if (($family === 'Table') && in_array($suffix, ['Exists', 'DoesNotExist'], strict: true)) {
                    $parameters[] = '?\\Closure $checkActionUsing = null';
                    $assertionArguments .= ', $checkActionUsing';
                }
                $hasRecord = ($family === 'Table') && (! in_array($suffix, ['Mounted', 'NotMounted', 'Halted'], strict: true));
                if ($hasRecord) {
                    $parameters[] = '$record = null';
                }
                $parse = ($family === 'TableBulk')
                    ? '$this->parseNestedTableBulkActions($actions)'
                    : '$this->parseNestedTableActions($actions' . ($hasRecord ? ', $record' : '') . ')';
            }

            $canonical = 'assertAction' . ($suffix === 'Halted' ? 'Mounted' : $suffix);
            $code = 'function (' . implode(', ', $parameters) . '): static { $actions = ' . $parse . '; $this->' . $canonical . '(' . $assertionArguments . '); return $this; }';
        }

        /** @var Expression $statement */
        $statement = (new ParserFactory)->createForNewestSupportedVersion()->parse('<?php ' . $code . ';')[0];

        /** @var ClosureExpression */
        return $statement->expr;
    }
}
