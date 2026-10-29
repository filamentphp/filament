<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
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
use PhpParser\Node\UnionType;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RemoveExportActionCsvDelimiterRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove ineffective export delimiter configuration while preserving expression evaluation and custom dispatch', [
            new CodeSample('$action->csvDelimiter(delimiter())', '(static fn ($action) => method_exists($action, "csvDelimiter") ? $action->csvDelimiter(...) : static fn (string|\\Closure|null $delimiter) => $action)($action)(delimiter())'),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, PropertyFetch::class, NullsafePropertyFetch::class, ArrayDimFetch::class, StaticCall::class, Isset_::class, Empty_::class, Coalesce::class];
    }

    /** @param Expr $node */
    public function refactor(Node $node): ?Node
    {
        $this->reservedVariables = [];

        if ($node instanceof Coalesce) {
            return $this->replaceExpression($node->left, static fn (Expr $expression): Expr => new Coalesce($expression, clone $node->right));
        }

        if ($node instanceof Empty_) {
            return $this->replaceExpression($node->expr, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('true')) : new Empty_($expression));
        }

        if ($node instanceof Isset_) {
            $result = null;
            $changed = false;

            foreach ($node->vars as $variable) {
                $replacement = $this->replaceExpression($variable, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('false')) : new Isset_([$expression]));
                $changed = $changed || ($replacement !== null);
                $replacement ??= new Isset_([$variable]);
                $result = $result ? new BooleanAnd($result, $replacement) : $replacement;
            }

            return $changed ? $result : null;
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isName($node->class, 'parent')) || (! $this->isDelimiterCall($node))) {
                return null;
            }

            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $parent = $context?->getParentClass();

            if ((! $context?->isTrait()) && ((! $parent) || (! $this->isExportType(new ObjectType($parent->getName()))))) {
                return null;
            }

            return $this->replaceParentCall($node);
        }

        return $this->replaceExpression($node);
    }

    private function replaceExpression(Expr $node, ?Closure $context = null): ?Expr
    {
        $chain = [];
        $receiver = $node;
        $hasDelimiter = false;

        while ($this->isChainNode($receiver)) {
            $chain[] = $receiver;

            if (($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall) && $this->isDelimiterCall($receiver) && $this->isExportType($this->receiverType($receiver->var))) {
                $hasDelimiter = true;
            }

            $receiver = $receiver->var;
        }

        if (! $hasDelimiter) {
            return null;
        }

        return $this->replaceChain($receiver, array_reverse($chain), $context);
    }

    private function isChainNode(Node $node): bool
    {
        return $node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof PropertyFetch || $node instanceof NullsafePropertyFetch || $node instanceof ArrayDimFetch;
    }

    private function isDelimiterCall(MethodCall | NullsafeMethodCall | StaticCall $node): bool
    {
        return $node->name instanceof Identifier && $this->isName($node->name, 'csvDelimiter');
    }

    private function isExportType(Type $type): bool
    {
        $type = TypeCombinator::removeNull($type);

        return $type->isObject()->yes() && TypeCombinator::union(new ObjectType(ExportAction::class), new ObjectType(ExportBulkAction::class))->isSuperTypeOf($type)->yes();
    }

    private function receiverType(Expr $receiver): Type
    {
        $type = ScopeFetcher::fetch($receiver)->getType($receiver);

        if ($this->isExportType($type) || (! ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall)) || (! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
            return $type;
        }

        $owner = $this->receiverType($receiver->var);

        if (! $this->isExportType($owner)) {
            return $type;
        }

        // The removed method is no longer available to PHPStan on a v6 install.
        if ($this->isDelimiterCall($receiver)) {
            return $owner;
        }

        foreach ($owner->getObjectClassReflections() as $class) {
            if (! $class->hasNativeMethod($receiver->name->toString())) {
                return $type;
            }

            foreach ($class->getNativeMethod($receiver->name->toString())->getVariants() as $variant) {
                if (! $variant->getNativeReturnType() instanceof StaticType) {
                    return $type;
                }
            }
        }

        return $owner;
    }

    /** @param array<MethodCall | NullsafeMethodCall | PropertyFetch | NullsafePropertyFetch | ArrayDimFetch> $chain */
    private function replaceChain(Expr $receiver, array $chain, ?Closure $context = null): Expr
    {
        foreach ($chain as $index => $original) {
            $node = clone $original;

            if ($node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch) {
                // Lower the whole remaining chain, not just the removed call:
                // PHP skips following arguments and accesses when this receiver is null.
                $variable = $this->temporaryVariable();
                $normal = $node instanceof NullsafeMethodCall
                    ? new MethodCall($node->var, $node->name, $node->args, $node->getAttributes())
                    : new PropertyFetch($node->var, $node->name, $node->getAttributes());

                return new Ternary(
                    new Identical(new Assign($variable, $receiver), new ConstFetch(new Name('null'))),
                    $context ? $context(new ConstFetch(new Name('null')), true) : new ConstFetch(new Name('null')),
                    $this->replaceChain($variable, [$normal, ...array_slice($chain, $index + 1)], $context),
                );
            }

            if ($node instanceof MethodCall && $this->isDelimiterCall($node) && $this->isExportType($this->receiverType($node->var))) {
                $receiver = $this->replaceCall($receiver, $node);

                continue;
            }

            $node->var = $receiver;
            $receiver = $node;
        }

        return $context ? $context($receiver, false) : $receiver;
    }

    private function temporaryVariable(): Variable
    {
        $scopeVariables = (new NodeFinder)->findInstanceOf($this->getFile()->getNewStmts(), Variable::class);
        $names = [...$this->reservedVariables, ...array_map(static fn (Variable $variable) => $variable->name, $scopeVariables)];
        $index = 0;

        do {
            $name = 'exportActionForCsvDelimiter' . ++$index;
        } while (in_array($name, $names, true));

        // Reserve the name for other nullsafe segments generated in this expression.
        $this->reservedVariables[] = $name;

        return new Variable($name);
    }

    /** @var array<string> */
    private array $reservedVariables = [];

    private function replaceParentCall(StaticCall $node): Expr
    {
        // A trait may be consumed by an export action or an unrelated class.
        // Resolve its lexical parent at runtime and retain any real parent method.
        $parent = new ClassConstFetch(new Name('parent'), 'class');
        $isExport = new BooleanOr(
            new FuncCall(new FullyQualified('is_a'), [new Arg($parent), new Arg(new ClassConstFetch(new FullyQualified(ExportAction::class), 'class')), new Arg(new ConstFetch(new Name('true')))]),
            new FuncCall(new FullyQualified('is_a'), [new Arg($parent), new Arg(new ClassConstFetch(new FullyQualified(ExportBulkAction::class), 'class')), new Arg(new ConstFetch(new Name('true')))]),
        );
        $callable = new Ternary(
            new BooleanAnd($isExport, new BooleanNot(new FuncCall(new FullyQualified('method_exists'), [new Arg($parent), new Arg(new String_('csvDelimiter'))]))),
            new ArrowFunction([
                'params' => [new Param(new Variable('delimiter'), type: new UnionType([new Identifier('string'), new FullyQualified('Closure'), new Identifier('null')]))],
                'expr' => new Variable('this'),
            ]),
            // The literal dynamic spelling also keeps subsequent upgrade passes
            // from trying to migrate this already-guarded parent callable again.
            new StaticCall(new Name('parent'), new String_('csvDelimiter'), [new VariadicPlaceholder]),
        );

        return $node->isFirstClassCallable() ? $callable : new FuncCall($callable, $node->getArgs());
    }

    private function replaceCall(Expr $receiver, MethodCall $node): Expr
    {
        $action = new Variable('action');
        $result = new ArrowFunction([
            'static' => true,
            'params' => [new Param(new Variable('delimiter'), type: new UnionType([new Identifier('string'), new FullyQualified('Closure'), new Identifier('null')]))],
            'expr' => $action,
        ]);

        $result = new Ternary(
            new FuncCall(new FullyQualified('method_exists'), [new Arg($action), new Arg(new String_('csvDelimiter'))]),
            new MethodCall($action, 'csvDelimiter', [new VariadicPlaceholder]),
            $result,
        );

        // Bind the receiver separately, leaving every argument name and reference
        // available to a genuine override's original callable signature.
        $callable = new FuncCall(new ArrowFunction(['static' => true, 'params' => [new Param($action)], 'expr' => $result]), [new Arg($receiver)]);

        return $node->isFirstClassCallable() ? $callable : new FuncCall($callable, $node->getArgs());
    }
}
