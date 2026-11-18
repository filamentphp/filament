<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ClosureUse;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RemoveExportActionCsvDelimiterRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /** @param MethodCall | NullsafeMethodCall | StaticCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || (! $this->isName($node->name, 'csvDelimiter')) || $node->isFirstClassCallable() || count($node->args) !== 1) {
            return null;
        }

        $argument = $node->args[0];

        if ($argument->unpack || ($argument->name && $argument->name->toString() !== 'delimiter')) {
            return null;
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isName($node->class, 'parent'))) {
                return null;
            }

            $parent = ScopeFetcher::fetch($node)->getClassReflection()?->getParentClass();

            if ((! $parent) || (! $this->isExportType(new ObjectType($parent->getName())))) {
                return null;
            }

            $receiver = new Variable('this');
        } else {
            if (! $this->isExportType($this->receiverType($node->var))) {
                return null;
            }

            $receiver = $node->var;
        }

        $delimiter = $argument->value;
        $isInert = $delimiter instanceof String_ || $delimiter instanceof Int_ || $delimiter instanceof Float_ || $delimiter instanceof ConstFetch || $delimiter instanceof Closure || $delimiter instanceof ArrowFunction || ($delimiter instanceof Variable && is_string($delimiter->name));
        $hasNullsafeReceiver = (new NodeFinder)->findFirst([$receiver], static fn (Node $expression): bool => $expression instanceof NullsafeMethodCall || $expression instanceof NullsafePropertyFetch) !== null;

        if ($isInert && (! $node instanceof NullsafeMethodCall) && (! $hasNullsafeReceiver)) {
            return $receiver;
        }

        // Keep scope-sensitive expressions available for an explicit migration.
        if ((! $isInert) && (new NodeFinder)->findFirst([$delimiter], static fn (Node $expression): bool => $expression instanceof Expr\Assign || $expression instanceof Expr\AssignRef || $expression instanceof Expr\AssignOp || $expression instanceof Expr\PreInc || $expression instanceof Expr\PostInc || $expression instanceof Expr\PreDec || $expression instanceof Expr\PostDec || $expression instanceof Expr\Yield_ || $expression instanceof Expr\YieldFrom || $expression instanceof Closure || $expression instanceof ArrowFunction) !== null) {
            return null;
        }

        $callback = new ArrowFunction(['expr' => $isInert ? new ConstFetch(new Name('null')) : $delimiter]);

        if (! $isInert) {
            $uses = [];

            foreach ((new NodeFinder)->findInstanceOf($delimiter, Variable::class) as $variable) {
                if (! is_string($variable->name)) {
                    return null;
                }

                if (! in_array($variable->name, ['this', 'GLOBALS', '_SERVER', '_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_REQUEST', '_ENV'], true)) {
                    $uses[$variable->name] = new ClosureUse($variable, byRef: true);
                }
            }

            // The callback runs immediately. Reference captures also preserve
            // mutations made by a delimiter factory's reference parameters.
            if ($uses) {
                $callback = new Closure(['uses' => array_values($uses), 'stmts' => [new Return_($delimiter)]]);
            }
        }

        return ($node instanceof NullsafeMethodCall)
            ? new NullsafeMethodCall($receiver, 'tap', [new Arg($callback)])
            : new MethodCall($receiver, 'tap', [new Arg($callback)]);
    }

    private function receiverType(Expr $receiver): Type
    {
        $type = $this->getType($receiver);

        if ($this->isExportType($type) || (! ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall)) || (! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
            return $type;
        }

        $owner = $this->receiverType($receiver->var);

        if (! $this->isExportType($owner)) {
            return $type;
        }

        // The removed fluent setter has no return type on the target version.
        if ($this->isName($receiver->name, 'csvDelimiter')) {
            return $owner;
        }

        foreach ($owner->getObjectClassReflections() as $class) {
            if ((! $class->hasNativeMethod($receiver->name->toString())) || (! $class->getNativeMethod($receiver->name->toString())->getVariants()[0]->getNativeReturnType() instanceof StaticType)) {
                return $type;
            }
        }

        return $owner;
    }

    private function isExportType(Type $type): bool
    {
        $type = TypeCombinator::removeNull($type);

        if (! TypeCombinator::union(new ObjectType(ExportAction::class), new ObjectType(ExportBulkAction::class))->isSuperTypeOf($type)->yes()) {
            return false;
        }

        foreach ($type->getObjectClassReflections() as $class) {
            if ($class->hasNativeMethod('csvDelimiter')) {
                return false;
            }
        }

        return true;
    }
}
