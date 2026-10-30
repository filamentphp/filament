<?php

namespace Filament\Upgrade\Rector;

use Closure as ContextClosure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\CanGroupRecords;
use Filament\Tables\Concerns\HasColumnManager;
use Filament\Upgrade\Support\UnnormalizedUnionTypeResolver;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ClosureUse;
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
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameThinActionAndTableAliasesRector extends AbstractRector
{
    /** @var array<string, array{class-string, string, string}> */
    private const METHODS = [
        'mountedactionhasform' => [InteractsWithActions::class, 'mountedActionHasSchema', 'bool'],
        'getmountedactionform' => [InteractsWithActions::class, 'getMountedActionSchema', 'schema'],
        'updatedtoggledtablecolumns' => [HasColumnManager::class, 'applyTableColumnManager', 'void'],
        'gettoggledtablecolumnssessionkey' => [HasColumnManager::class, 'getTableColumnsSessionKey', 'string'],
        'updatedtablegroupcolumn' => [CanGroupRecords::class, 'updatedTableGrouping', 'void'],
    ];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private UnnormalizedUnionTypeResolver $unnormalizedUnionTypeResolver,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Migrate thin action and table aliases without activating custom lifecycle overrides', [
            new CodeSample('$table->updatedToggledTableColumns();', '$table->applyTableColumnManager();'),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class, PropertyFetch::class, NullsafePropertyFetch::class, ArrayDimFetch::class, Coalesce::class, Empty_::class, Isset_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (ScopeFetcher::fetch($node)->getClassReflection()?->isTrait()) {
            if ($node instanceof Coalesce) {
                return $this->replaceNullsafeChain($node->left, static fn (Expr $expression): Expr => new Coalesce($expression, $node->right));
            }

            if ($node instanceof Empty_) {
                return $this->replaceNullsafeChain($node->expr, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('true')) : new Empty_($expression));
            }

            if ($node instanceof Isset_) {
                $changed = false;
                $result = null;

                foreach ($node->vars as $variable) {
                    $replacement = $this->replaceNullsafeChain($variable, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('false')) : new Isset_([$expression]));
                    $changed = $changed || ($replacement !== null);
                    $replacement ??= new Isset_([$variable]);
                    $result = $result ? new BooleanAnd($result, $replacement) : $replacement;
                }

                return $changed ? $result : null;
            }

            if (($node instanceof Expr) && ($replacement = $this->replaceNullsafeChain($node))) {
                return $replacement;
            }
        }

        return $node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof StaticCall ? $this->replaceCall($node) : null;
    }

    private function replaceNullsafeChain(Expr $expression, ?ContextClosure $context = null): ?Expr
    {
        $chain = [];
        $receiver = $expression;
        $hasAlias = false;
        $hasNullsafe = false;

        while ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall || $receiver instanceof PropertyFetch || $receiver instanceof NullsafePropertyFetch || $receiver instanceof ArrayDimFetch) {
            $chain[] = $receiver;
            $hasAlias = $hasAlias || (($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall) && ($receiver->name instanceof Identifier) && isset(self::METHODS[strtolower($receiver->name->toString())]));
            $hasNullsafe = $hasNullsafe || $receiver instanceof NullsafeMethodCall || $receiver instanceof NullsafePropertyFetch;
            $receiver = $receiver->var;
        }

        return $hasAlias && $hasNullsafe ? $this->lowerNullsafeChain($receiver, array_reverse($chain), $context) : null;
    }

    /** @param array<MethodCall | NullsafeMethodCall | PropertyFetch | NullsafePropertyFetch | ArrayDimFetch> $chain */
    private function lowerNullsafeChain(Expr $receiver, array $chain, ?ContextClosure $context): Expr
    {
        foreach ($chain as $index => $original) {
            $node = clone $original;

            if ($node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch) {
                $names = array_map(static fn (Variable $variable) => $variable->name, (new NodeFinder)->findInstanceOf($this->getFile()->getNewStmts(), Variable::class));
                $number = 1;

                while (in_array('thinAliasReceiver' . $number, $names, true)) {
                    $number++;
                }

                $variable = new Variable('thinAliasReceiver' . $number);
                $normal = $node instanceof NullsafeMethodCall ? new MethodCall($node->var, $node->name, $node->args, $node->getAttributes()) : new PropertyFetch($node->var, $node->name, $node->getAttributes());

                return new Ternary(
                    new Identical(new Assign($variable, $receiver), new ConstFetch(new Name('null'))),
                    $context ? $context(new ConstFetch(new Name('null')), true) : new ConstFetch(new Name('null')),
                    $this->lowerNullsafeChain($variable, [$normal, ...array_slice($chain, $index + 1)], $context),
                );
            }

            if (($node instanceof MethodCall) && ($replacement = $this->replaceCall($node, $receiver))) {
                $receiver = $replacement;

                continue;
            }

            $node->var = $receiver;
            $receiver = $node;
        }

        return $context ? $context($receiver, false) : $receiver;
    }

    private function replaceCall(MethodCall | NullsafeMethodCall | StaticCall $node, ?Expr $boundReceiver = null): ?Expr
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = strtolower($node->name->toString());

        if (! isset(self::METHODS[$methodName])) {
            return null;
        }

        [$owner, $replacement, $result] = self::METHODS[$methodName];
        $context = ScopeFetcher::fetch($node)->getClassReflection();
        $isRelative = $node instanceof StaticCall;
        // Trait receiver types can be unresolved even for `$this`; keep the
        // ownership decision in the generated runtime guard.
        $isSharedCall = $context?->isTrait() ?? false;

        if ($isRelative) {
            $function = ScopeFetcher::fetch($node)->getFunction();

            if ((! $node->class instanceof Name) || (! $this->isNames($node->class, ['parent', 'self', 'static'])) || (! $context) || (! $function instanceof MethodReflection) || $function->isStatic()) {
                return null;
            }

            $classes = [$context];

            foreach ($classes as $class) {
                $target = $this->isName($node->class, 'parent') ? $class->getParentClass() : $class;

                if ((! $isSharedCall) && ((! $target) || (! $this->owns($target, $owner, $methodName, $replacement)))) {
                    return null;
                }
            }

            // The removed parent wrapper dispatched virtually, not via `parent::canonical()`.
            $receiver = new Variable('this');
        } else {
            $receiver = $boundReceiver ?? $node->var;
            $classes = TypeCombinator::removeNull($this->getType($node->var))->getObjectClassReflections();

            foreach ($classes as $class) {
                if ((! $isSharedCall) && (! $this->owns($class, $owner, $methodName, $replacement))) {
                    return null;
                }
            }

            // PHPStan can normalize `Base | CustomBase` to `Base`.
            foreach ($this->unnormalizedUnionTypeResolver->resolve($this->getFile()->getNewStmts()) as $types) {
                foreach ($types as $type) {
                    if ($isSharedCall || (! TypeCombinator::removeNull($this->getType($node->var))->isSuperTypeOf($type)->yes())) {
                        continue;
                    }

                    foreach ($type->getObjectClassReflections() as $class) {
                        if (! $this->owns($class, $owner, $methodName, $replacement)) {
                            return null;
                        }
                    }
                }
            }
        }

        if ((! $isSharedCall) && (! count($classes))) {
            return null;
        }

        // Keep protected helpers inside their original access boundary, including
        // legal calls on another related instance from the same class scope.
        if ((! $isSharedCall) && in_array($methodName, ['getmountedactionform', 'gettoggledtablecolumnssessionkey'], true) && (! $isRelative) && ((! $receiver instanceof Variable) || ($receiver->name !== 'this'))) {
            if (! $context) {
                return null;
            }

            foreach ($classes as $class) {
                if (($class->getName() !== $context->getName()) && (! $class->isSubclassOf($context->getName())) && (! $context->isSubclassOf($class->getName()))) {
                    return null;
                }
            }
        }

        $parameters = $this->parameters($methodName);
        $forwarded = array_map(static fn (Param $parameter): Arg => new Arg(clone $parameter->var), $parameters);

        if ($node->isFirstClassCallable() || $isSharedCall) {
            $target = new Variable('receiver');
            $call = new MethodCall($target, $replacement, $forwarded);
            $adapter = $result === 'void'
                ? new Closure(['static' => true, 'params' => $parameters, 'uses' => [new ClosureUse($target)], 'returnType' => new Identifier('void'), 'stmts' => [new Expression($call)]])
                : new ArrowFunction(['static' => true, 'params' => $parameters, 'returnType' => $result === 'schema' ? new NullableType(new FullyQualified(Schema::class)) : new Identifier($result), 'expr' => $call]);

            if ($isSharedCall) {
                $legacy = $isRelative
                    ? new StaticCall($node->class, new String_($node->name->toString()), [new VariadicPlaceholder])
                    : new MethodCall($target, new String_($node->name->toString()), [new VariadicPlaceholder]);
                $identity = $isRelative ? new ClassConstFetch($node->class, 'class') : $target;
                $isOwner = new FuncCall(new FullyQualified('in_array'), [new Arg(new String_($owner)), new Arg(new FuncCall(new FullyQualified('class_uses_recursive'), [new Arg($identity)])), new Arg(new ConstFetch(new Name('true')))]);
                $hasLegacy = new FuncCall(new FullyQualified('method_exists'), [new Arg($identity), new Arg(new String_($node->name->toString()))]);

                // Shared traits may also serve unrelated native/magic methods.
                // Preserve those and custom aliases instead of activating overrides.
                $adapter = new Ternary(new BooleanOr(new BooleanNot($isOwner), $hasLegacy), $legacy, $adapter);
            }

            $callable = new FuncCall(new ArrowFunction(['static' => ! $isRelative, 'params' => [new Param($target)], 'expr' => $adapter]), [new Arg($receiver)]);

            return $node->isFirstClassCallable() ? $callable : new FuncCall($callable, $node->getArgs());
        }

        $arguments = $node->getArgs();

        // Normalize through the old signature: evaluate ignored extras but never
        // pass them into newly available canonical parameters. Keeping the call
        // in its original nullsafe chain also preserves downstream short-circuiting.
        if (count($arguments) || count($parameters)) {
            $adapter = new ArrowFunction([
                'static' => true,
                'params' => $parameters,
                'returnType' => new Identifier('array'),
                'expr' => new Array_(array_map(static fn (Arg $argument): ArrayItem => new ArrayItem($argument->value), $forwarded)),
            ]);
            $arguments = [new Arg(new FuncCall($adapter, $arguments), unpack: true)];
        }

        return $node instanceof NullsafeMethodCall
            ? new NullsafeMethodCall($receiver, $replacement, $arguments)
            : new MethodCall($receiver, $replacement, $arguments);
    }

    /** @return list<Param> */
    private function parameters(string $methodName): array
    {
        $parameters = [];

        if ($methodName === 'getmountedactionform') {
            $parameters[] = new Param(new Variable('actionNestingIndex'), new ConstFetch(new Name('null')), new NullableType(new Identifier('int')));
        }

        if (in_array($methodName, ['mountedactionhasform', 'getmountedactionform'], true)) {
            $parameters[] = new Param(new Variable('mountedAction'), new ConstFetch(new Name('null')), new NullableType(new FullyQualified(Action::class)));
        }

        return $parameters;
    }

    private function owns(ClassReflection $class, string $owner, string $methodName, string $replacement): bool
    {
        $hasOwner = $class->getName() === $owner;

        foreach ($class->getAncestors() as $ancestor) {
            $hasOwner = $hasOwner || $ancestor->hasTraitUse($owner);
        }

        if ((! $hasOwner) || (! $class->hasNativeMethod($replacement))) {
            return false;
        }

        if (! $class->hasNativeMethod($methodName)) {
            return true;
        }

        $method = $class->getNativeMethod($methodName);

        // An application can adapt a surviving trait method to the removed name.
        // That is its own dispatch contract, not the deleted forwarding wrapper.
        return ($method->getDeclaringClass()->getName() === $owner)
            || (method_exists($method, 'getDeclaringTrait') && ($method->getDeclaringTrait()?->getName() === $owner) && $this->reflectionProvider->getClass($owner)->hasNativeMethod($methodName));
    }
}
