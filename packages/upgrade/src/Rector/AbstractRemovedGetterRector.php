<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

/** @internal Shared matching for the two removed zero-argument getters. */
abstract class AbstractRemovedGetterRector extends AbstractRector
{
    abstract protected function owner(): string;

    abstract protected function method(): string;

    abstract protected function result(Expr $receiver, bool $isNullsafe = false): Expr;

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /** @param MethodCall | NullsafeMethodCall | StaticCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || (! $this->isName($node->name, $this->method())) || count($node->args)) {
            return null;
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isNames($node->class, ['parent', 'self', 'static']))) {
                return null;
            }

            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $class = $this->isName($node->class, 'parent') ? $context?->getParentClass() : $context;

            if ((! $class) || $class->hasNativeMethod($this->method()) || (! (new ObjectType($this->owner()))->isSuperTypeOf(new ObjectType($class->getName()))->yes())) {
                return null;
            }

            return $this->result(new Variable('this'));
        }

        $type = TypeCombinator::removeNull($this->getType($node->var));

        if (! (new ObjectType($this->owner()))->isSuperTypeOf($type)->yes()) {
            return null;
        }

        foreach ($type->getObjectClassReflections() as $class) {
            if ($class->hasNativeMethod($this->method())) {
                return null;
            }
        }

        $hasNullsafeReceiver = (new NodeFinder)->findFirst([$node->var], static fn (Node $receiver): bool => $receiver instanceof NullsafeMethodCall || $receiver instanceof NullsafePropertyFetch) !== null;

        return $this->result($node->var, $node instanceof NullsafeMethodCall || $hasNullsafeReceiver);
    }
}
