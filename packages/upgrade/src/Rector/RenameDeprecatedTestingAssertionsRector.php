<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
use Livewire\Features\SupportTesting\Testable;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure as ClosureExpression;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\UnionType;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedTestingAssertionsRector extends AbstractRector
{
    /** @var array<string, string> */
    private const ACTION_ASSERTIONS = [
        'setActionData' => 'fillForm',
        'assertActionDataSet' => 'assertSchemaStateSet',
        'assertHasActionErrors' => 'assertHasFormErrors',
        'assertHasNoActionErrors' => 'assertHasNoFormErrors',
    ];

    /**
     * @var array<string, array{string, class-string}>
     */
    private const ASSERTIONS = [
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename deprecated Filament testing macros on known Livewire test receivers', [
            new CodeSample(
                '$testable->assertFormFieldIsDisabled(key: "title");',
                '$testable->assertFormFieldDisabled(key: "title");',
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        // Macro keys are case-sensitive, unlike ordinary PHP methods.
        if ((! $node->name instanceof Identifier) || ((! isset(self::ASSERTIONS[$node->name->name])) && (! isset(self::ACTION_ASSERTIONS[$node->name->name])))) {
            return null;
        }

        $isActionAssertion = isset(self::ACTION_ASSERTIONS[$node->name->name]);

        if ($isActionAssertion) {
            // The replacements belong to other mixins, not `TestsActions`.
            if (! $this->isTestable($node->var, allowInherited: true)) {
                return null;
            }
        } else {
            [$replacement, $mixin] = self::ASSERTIONS[$node->name->name];

            if ((! $this->isTestable($node->var)) && (! $this->isExactType($node->var, $mixin))) {
                return null;
            }
        }

        // Custom registrations can replace either macro. Native unions may
        // normalize an owning class and its overriding subclass to the owner.
        if ((new NodeFinder)->findFirst($this->getFile()->getNewStmts(), function (Node $candidate): bool {
            if (($candidate instanceof StaticCall) || ($candidate instanceof MethodCall) || ($candidate instanceof NullsafeMethodCall)) {
                return $candidate->name instanceof Identifier && in_array(strtolower($candidate->name->name), ['macro', 'mixin', '__call', '__callstatic', 'macrocall'], strict: true);
            }

            if (! $candidate instanceof UnionType) {
                return false;
            }

            foreach ($candidate->types as $type) {
                foreach ([Testable::class, TestsForms::class, TestsActions::class, TestsTableActions::class, TestsBulkActions::class] as $owner) {
                    if ((new ObjectType($owner))->isSuperTypeOf($this->getType($type))->yes()) {
                        return true;
                    }
                }
            }

            return false;
        })) {
            return null;
        }

        if ($isActionAssertion) {
            return $this->adaptActionAssertion($node);
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    private function adaptActionAssertion(MethodCall | NullsafeMethodCall $node): Expr
    {
        $method = $node->name->name;
        $parameter = in_array($method, ['setActionData', 'assertActionDataSet'], strict: true) ? 'data' : 'keys';
        $parameterType = $method === 'assertActionDataSet'
            ? new UnionType([new Identifier('array'), new FullyQualified(Closure::class)])
            : new Identifier('array');
        $testable = new Variable('testable');
        $null = new ConstFetch(new Name('null'));

        // Retain the legacy callback's arity, types, defaults, named arguments,
        // ignored positional extras and fluent identity, even with custom
        // canonical macros. Bind just as `Macroable` binds its callbacks.
        $callback = new ClosureExpression([
            'params' => [new Param(new Variable($parameter), default: $parameter === 'keys' ? new Expr\Array_ : null, type: $parameterType)],
            'returnType' => new Identifier('static'),
            'stmts' => [
                new Expression(new MethodCall(new Variable('this'), self::ACTION_ASSERTIONS[$method], [new Arg(new Variable($parameter))])),
                new Return_(new Variable('this')),
            ],
        ]);
        $factory = new ArrowFunction([
            'static' => true,
            'params' => [new Param($testable, type: new NullableType(new FullyQualified(Testable::class)))],
            'returnType' => new NullableType(new FullyQualified(Closure::class)),
            'expr' => new Ternary(
                new Identical($testable, $null),
                $null,
                new MethodCall($callback, 'bindTo', [new Arg($testable), new Arg(new ClassConstFetch($testable, 'class'))]),
            ),
        ]);
        $bound = new FuncCall($factory, [new Arg($node->var)]);

        if ($node->isFirstClassCallable()) {
            return $bound;
        }

        return $this->isNullsafeChain($node)
            ? new NullsafeMethodCall($bound, '__invoke', $node->args)
            : new FuncCall($bound, $node->args);
    }

    private function isNullsafeChain(Expr $expression): bool
    {
        if (($expression instanceof NullsafeMethodCall) || ($expression instanceof NullsafePropertyFetch)) {
            return true;
        }

        return (($expression instanceof MethodCall) || ($expression instanceof PropertyFetch) || ($expression instanceof ArrayDimFetch))
            && $this->isNullsafeChain($expression->var);
    }

    private function isTestable(Expr $receiver, bool $allowInherited = false): bool
    {
        if (($receiver instanceof MethodCall) || ($receiver instanceof NullsafeMethodCall)) {
            if ((! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
                return false;
            }

            if ($this->isTestable($receiver->var, $allowInherited)) {
                $method = $receiver->name->name;

                if ($allowInherited) {
                    $reflections = TypeCombinator::removeNull(ScopeFetcher::fetch($receiver->var)->getType($receiver->var))->getObjectClassReflections();
                    $reflection = count($reflections) === 1 ? $reflections[0] : null;

                    if ($reflection?->hasNativeMethod($method) && ($reflection->getNativeMethod($method)->getDeclaringClass()->getName() !== Testable::class)) {
                        return $reflection->getNativeMethod($method)->getVariants()[0]->getReturnType() instanceof StaticType;
                    }
                }

                // Do not trust `__call()`'s `$this` PHPDoc for arbitrary macros,
                // which can return other objects instead of the test receiver.
                if (isset(self::ASSERTIONS[$method]) || isset(self::ACTION_ASSERTIONS[$method]) || in_array($method, self::ACTION_ASSERTIONS, strict: true) || in_array($method, array_column(self::ASSERTIONS, 0), strict: true)) {
                    return true;
                }

                if (in_array($method, ['fillForm', 'mountAction', 'callAction', 'mountTableAction', 'callTableAction', 'mountTableBulkAction', 'callTableBulkAction', 'assertDispatched', 'assertNotDispatched', 'assertDispatchedTo'], strict: true)) {
                    return true;
                }

                $reflection = (new ObjectType(Testable::class))->getClassReflection();

                if (! $reflection?->hasNativeMethod($method)) {
                    return false;
                }

                return $reflection->getNativeMethod($method)->getVariants()[0]->getReturnType() instanceof StaticType;
            }
        }

        if ($allowInherited) {
            $type = TypeCombinator::removeNull(ScopeFetcher::fetch($receiver)->getType($receiver));

            if (! (new ObjectType(Testable::class))->isSuperTypeOf($type)->yes()) {
                return false;
            }

            $reflection = $type->getObjectClassReflections();

            if (count($reflection) !== 1) {
                return false;
            }

            $reflection = $reflection[0];

            foreach (['__call', 'macroCall', ...array_keys(self::ACTION_ASSERTIONS)] as $method) {
                if ($reflection->hasNativeMethod($method) && ($reflection->getNativeMethod($method)->getDeclaringClass()->getName() !== Testable::class)) {
                    return false;
                }
            }

            return true;
        }

        return $this->isExactType($receiver, Testable::class);
    }

    /**
     * @param  class-string  $class
     */
    private function isExactType(Expr $receiver, string $class): bool
    {
        // Custom subclasses may override native methods or magic dispatch.
        $type = TypeCombinator::removeNull(ScopeFetcher::fetch($receiver)->getType($receiver));

        return $type->isObject()->yes() && ($type->getObjectClassNames() === [$class]);
    }
}
