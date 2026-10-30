<?php

namespace Filament\Upgrade\Rector;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
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
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

/** @internal Shared lowering for the two removed zero-argument getters. */
abstract class AbstractRemovedGetterRector extends AbstractRector
{
    abstract protected function owner(): string;

    abstract protected function method(): string;

    abstract protected function result(Expr $receiver): Expr;

    /** @var array<string> */
    private array $reservedVariables = [];

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, PropertyFetch::class, NullsafePropertyFetch::class, ArrayDimFetch::class, StaticCall::class, Coalesce::class, Empty_::class, Isset_::class];
    }

    public function refactor(Node $node): ?Node
    {
        $this->reservedVariables = [];

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isNames($node->class, ['parent', 'self', 'static'])) || (! $this->isGetter($node))) {
                return null;
            }

            $class = ScopeFetcher::fetch($node)->getClassReflection();
            $owner = $this->isName($node->class, 'parent') ? $class?->getParentClass() : $class;

            if ((! $class?->isTrait()) && ((! $owner) || (! (new ObjectType($this->owner()))->isSuperTypeOf(new ObjectType($owner->getName()))->yes()))) {
                return null;
            }

            $identity = new Expr\ClassConstFetch($node->class, 'class');
            $hasMethod = new FuncCall(new FullyQualified('method_exists'), [new Arg($identity), new Arg(new String_($this->method()))]);

            if ($class->isTrait()) {
                // A shared trait's unrelated consumers retain lexical/late-static dispatch.
                $hasMethod = new Expr\BinaryOp\BooleanOr(
                    new Expr\BooleanNot(new FuncCall(new FullyQualified('is_a'), [new Arg($identity), new Arg(new Expr\ClassConstFetch(new FullyQualified($this->owner()), 'class')), new Arg(new ConstFetch(new Name('true')))])),
                    $hasMethod,
                );
            }

            $callable = new Ternary(
                $hasMethod,
                new StaticCall($node->class, new String_($node->name->toString()), [new VariadicPlaceholder]),
                new ArrowFunction(['expr' => $this->result(new Variable('this'))]),
            );

            return $node->isFirstClassCallable() ? $callable : new FuncCall($callable, $node->getArgs());
        }

        if ($node instanceof Coalesce) {
            return $this->replace($node->left, static fn (Expr $expression): Expr => new Coalesce($expression, $node->right));
        }

        if ($node instanceof Empty_) {
            return $this->replace($node->expr, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('true')) : new Empty_($expression));
        }

        if ($node instanceof Isset_) {
            $changed = false;
            $result = null;

            foreach ($node->vars as $variable) {
                $replacement = $this->replace($variable, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('false')) : new Isset_([$expression]));
                $changed = $changed || ($replacement !== null);
                $replacement ??= new Isset_([$variable]);
                $result = $result ? new Expr\BinaryOp\BooleanAnd($result, $replacement) : $replacement;
            }

            return $changed ? $result : null;
        }

        return $node instanceof Expr ? $this->replace($node) : null;
    }

    private function isGetter(MethodCall | NullsafeMethodCall | StaticCall $node): bool
    {
        return $node->name instanceof Identifier && $this->isName($node->name, $this->method());
    }

    private function isOwner(Expr $receiver): bool
    {
        $type = TypeCombinator::removeNull($this->getType($receiver));
        $context = ScopeFetcher::fetch($receiver)->getClassReflection();

        // Trait receiver types can be unresolved even for `$this`; the generated
        // ownership guard preserves native and magic calls on other consumers.
        if ($context?->isTrait()) {
            return true;
        }

        return (new ObjectType($this->owner()))->isSuperTypeOf($type)->yes();
    }

    private function isChain(Node $node): bool
    {
        return $node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof PropertyFetch || $node instanceof NullsafePropertyFetch || $node instanceof ArrayDimFetch;
    }

    private function replace(Expr $expression, ?Closure $context = null): ?Expr
    {
        $chain = [];
        $receiver = $expression;
        $found = false;

        while ($this->isChain($receiver)) {
            $chain[] = $receiver;
            $found = $found || (($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall) && $this->isGetter($receiver) && $this->isOwner($receiver->var));
            $receiver = $receiver->var;
        }

        return $found ? $this->lower($receiver, array_reverse($chain), $context) : null;
    }

    /** @param array<MethodCall | NullsafeMethodCall | PropertyFetch | NullsafePropertyFetch | ArrayDimFetch> $chain */
    private function lower(Expr $receiver, array $chain, ?Closure $context): Expr
    {
        foreach ($chain as $index => $original) {
            $node = clone $original;

            if ($node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch) {
                $variable = $this->temporaryVariable();
                $normal = $node instanceof NullsafeMethodCall ? new MethodCall($node->var, $node->name, $node->args, $node->getAttributes()) : new PropertyFetch($node->var, $node->name, $node->getAttributes());

                return new Ternary(
                    new Identical(new Assign($variable, $receiver), new ConstFetch(new Name('null'))),
                    $context ? $context(new ConstFetch(new Name('null')), true) : new ConstFetch(new Name('null')),
                    $this->lower($variable, [$normal, ...array_slice($chain, $index + 1)], $context),
                );
            }

            if ($node instanceof MethodCall && $this->isGetter($node) && $this->isOwner($node->var)) {
                $boundReceiver = new Variable('component');
                $hasMethod = new FuncCall(new FullyQualified('method_exists'), [new Arg($boundReceiver), new Arg(new String_($this->method()))]);

                if (ScopeFetcher::fetch($node)->getClassReflection()?->isTrait()) {
                    // Shared traits retain native and magic dispatch on unrelated consumers.
                    $hasMethod = new Expr\BinaryOp\BooleanOr(
                        new Expr\BooleanNot(new FuncCall(new FullyQualified('is_a'), [new Arg($boundReceiver), new Arg(new Expr\ClassConstFetch(new FullyQualified($this->owner()), 'class'))])),
                        $hasMethod,
                    );
                }

                $callable = new FuncCall(new ArrowFunction([
                    'static' => true,
                    'params' => [new Param($boundReceiver)],
                    'expr' => new Ternary(
                        $hasMethod,
                        // Dynamic literal spelling makes the custom-dispatch branch idempotent.
                        new MethodCall($boundReceiver, new String_($node->name->toString()), [new VariadicPlaceholder]),
                        new ArrowFunction(['static' => true, 'expr' => $this->result($boundReceiver)]),
                    ),
                ]), [new Arg($receiver)]);
                $receiver = $node->isFirstClassCallable() ? $callable : new FuncCall($callable, $node->getArgs());

                continue;
            }

            $node->var = $receiver;
            $receiver = $node;
        }

        return $context ? $context($receiver, false) : $receiver;
    }

    private function temporaryVariable(): Variable
    {
        $variables = (new NodeFinder)->findInstanceOf($this->getFile()->getNewStmts(), Variable::class);
        $names = [...$this->reservedVariables, ...array_map(static fn (Variable $variable) => $variable->name, $variables)];
        $index = 0;

        do {
            $name = 'componentForRemovedGetter' . ++$index;
        } while (in_array($name, $names, true));

        $this->reservedVariables[] = $name;

        return new Variable($name);
    }
}
