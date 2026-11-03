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
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Closure as ClosureExpression;
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
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class RenameDeprecatedTestingAssertionsRector extends AbstractRector
{
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
        if ((! $node->name instanceof Identifier) || (! isset(self::ASSERTIONS[$node->name->name]))) {
            return null;
        }

        [$replacement, $mixin] = self::ASSERTIONS[$node->name->name];
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
                            && in_array($method, ['set', 'call', 'assertDispatched', 'assertNotDispatched', 'assertDispatchedTo'], strict: true)) {
                            return $ownerType;
                        }

                        return (new ObjectType(Testable::class))->isSuperTypeOf($type)->yes() ? $type : null;
                    }
                }

                if (isset(self::ASSERTIONS[$method]) || in_array($method, array_column(self::ASSERTIONS, 0), strict: true)) {
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
}
