<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Schema;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
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
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\UnionType;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenamePlainSchemaAliasesRector extends AbstractRector
{
    /** @var array<string, string> */
    private const METHODS = [
        'cacheform' => 'cacheSchema',
        'cacheinfolist' => 'cacheSchema',
        'hascachedform' => 'hasCachedSchema',
        'hascachedinfolist' => 'hasCachedSchema',
        'getform' => 'getSchema',
        'getinfolist' => 'getSchema',
        'getcachedforms' => 'getCachedSchemas',
        'getcachedinfolists' => 'getCachedSchemas',
        'iscachingforms' => 'isCachingSchemas',
        'getactiveformslocale' => 'getActiveSchemaLocale',
        'getoldformstate' => 'getOldSchemaState',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Migrate plain form and infolist schema aliases, preserving their callable contracts and custom dispatch', [
            new CodeSample('$component->getForm("form");', '(fn ($component) => method_exists($component, "getForm") ? $component->{"getForm"}(...) : fn (string $name): ?\\Filament\\Schemas\\Schema => $component->getSchema($name))($component)("form");'),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, PropertyFetch::class, NullsafePropertyFetch::class, ArrayDimFetch::class, StaticCall::class, Coalesce::class, Isset_::class, Empty_::class];
    }

    /** @param Expr $node */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isNames($node->class, ['parent', 'self', 'static'])) || (! $this->alias($node))) {
                return null;
            }

            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $owner = $this->isName($node->class, 'parent') ? $context?->getParentClass() : $context;

            if ((! $context?->isTrait()) && ((! $owner) || (! $this->owns($owner, $this->alias($node))))) {
                return null;
            }

            return $this->replaceCall(new Variable('this'), $node);
        }

        // Keep the nullsafe chain inside `??`, rather than turning an unset
        // property read into an eager read while lowering the chain.
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

        return $this->replaceExpression($node);
    }

    private function alias(MethodCall | NullsafeMethodCall | StaticCall $node): ?string
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $name = strtolower($node->name->toString());

        return isset(self::METHODS[$name]) ? $name : null;
    }

    private function owns(ClassReflection $class, string $alias): bool
    {
        $owner = str_contains($alias, 'infolist') ? InteractsWithInfolists::class : InteractsWithForms::class;

        do {
            if ($class->getName() === $owner) {
                return true;
            }

            foreach ($class->getTraits(true) as $trait) {
                if ($trait->getName() === $owner) {
                    return true;
                }
            }
        } while ($class = $class->getParentClass());

        return false;
    }

    private function supports(MethodCall | NullsafeMethodCall $node): bool
    {
        $alias = $this->alias($node);

        if (! $alias) {
            return false;
        }

        $type = TypeCombinator::removeNull($this->receiverType($node->var));
        $context = ScopeFetcher::fetch($node)->getClassReflection();

        // Trait receiver types can be unresolved even for `$this`; a runtime
        // guard distinguishes schema owners from unrelated consumers.
        if ($context?->isTrait()) {
            return true;
        }

        $classes = $type->getObjectClassReflections();

        return count($classes) && ! array_filter($classes, fn (ClassReflection $class): bool => ! $this->owns($class, $alias));
    }

    private function receiverType(Expr $receiver): Type
    {
        $type = $this->getType($receiver);

        if ((! ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall)) || (! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
            return $type;
        }

        $owner = $this->receiverType($receiver->var);

        foreach ($owner->getObjectClassReflections() as $class) {
            if ((! $class->hasNativeMethod($receiver->name->toString())) || (! $class->getNativeMethod($receiver->name->toString())->getVariants()[0]->getReturnType() instanceof StaticType)) {
                return $type;
            }
        }

        return count($owner->getObjectClassReflections()) ? $owner : $type;
    }

    private function replaceExpression(Expr $node, ?Closure $context = null): ?Expr
    {
        $chain = [];
        $receiver = $node;
        $supported = false;

        while ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall || $receiver instanceof PropertyFetch || $receiver instanceof NullsafePropertyFetch || $receiver instanceof ArrayDimFetch) {
            $chain[] = $receiver;
            $supported = $supported || (($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall) && $this->supports($receiver));
            $receiver = $receiver->var;
        }

        return $supported ? $this->replaceChain($receiver, array_reverse($chain), $context) : null;
    }

    /** @param array<MethodCall | NullsafeMethodCall | PropertyFetch | NullsafePropertyFetch | ArrayDimFetch> $chain */
    private function replaceChain(Expr $receiver, array $chain, ?Closure $context = null): Expr
    {
        foreach ($chain as $index => $original) {
            $node = clone $original;

            if ($node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch) {
                $names = array_map(static fn (Variable $variable) => $variable->name, (new NodeFinder)->findInstanceOf($this->getFile()->getNewStmts(), Variable::class));
                $number = 1;

                while (in_array('schemaAliasReceiver' . $number, $names, true)) {
                    $number++;
                }

                $variable = new Variable('schemaAliasReceiver' . $number);
                $normal = $node instanceof NullsafeMethodCall ? new MethodCall($node->var, $node->name, $node->args, $node->getAttributes()) : new PropertyFetch($node->var, $node->name, $node->getAttributes());

                return new Ternary(
                    new Identical(new Assign($variable, $receiver), new ConstFetch(new Name('null'))),
                    $context ? $context(new ConstFetch(new Name('null')), true) : new ConstFetch(new Name('null')),
                    $this->replaceChain($variable, [$normal, ...array_slice($chain, $index + 1)], $context),
                );
            }

            if ($node instanceof MethodCall && $this->supports($node)) {
                $receiver = $this->replaceCall($receiver, $node);

                continue;
            }

            $node->var = $receiver;
            $receiver = $node;
        }

        return $context ? $context($receiver, false) : $receiver;
    }

    private function replaceCall(Expr $receiver, MethodCall | StaticCall $node): Expr
    {
        $alias = $this->alias($node);
        $component = new Variable('schemaAliasComponent');
        $parameters = [];
        $arguments = [];
        $returnType = match ($alias) {
            'iscachingforms', 'hascachedform', 'hascachedinfolist' => new Identifier('bool'),
            'getcachedforms', 'getcachedinfolists' => new Identifier('array'),
            'getactiveformslocale' => new NullableType(new Identifier('string')),
            'getoldformstate' => new Identifier('mixed'),
            default => new NullableType(new FullyQualified(Schema::class)),
        };

        if (in_array($alias, ['getform', 'getinfolist', 'hascachedform', 'hascachedinfolist', 'cacheform', 'cacheinfolist', 'getoldformstate'], true)) {
            $name = $alias === 'getoldformstate' ? 'statePath' : 'name';
            $parameters[] = new Param(new Variable($name), type: new Identifier('string'));
            $arguments[] = new Arg(new Variable($name));
        }

        if (in_array($alias, ['cacheform', 'cacheinfolist'], true)) {
            $name = $alias === 'cacheform' ? 'form' : 'schema';
            $type = $alias === 'cacheform' ? new UnionType([new FullyQualified(Schema::class), new FullyQualified('Closure'), new Identifier('null')]) : new FullyQualified(Schema::class);
            $parameters[] = new Param(new Variable($name), type: $type);
            // Both arguments are always passed: explicit `null` unsets the cache.
            // Forward positionally, like the original wrapper, so a custom
            // canonical override can keep its own parameter names.
            $arguments[] = new Arg(new Variable($name));
        }

        $adapter = new ArrowFunction(['params' => $parameters, 'returnType' => $returnType, 'expr' => new MethodCall($component, self::METHODS[$alias], $arguments)]);
        $legacy = $node instanceof StaticCall ? new StaticCall($node->class, new String_($node->name->toString()), [new VariadicPlaceholder]) : new MethodCall($component, new String_($node->name->toString()), [new VariadicPlaceholder]);
        $target = $node instanceof StaticCall ? new ClassConstFetch($node->class, 'class') : $component;
        $condition = new FuncCall(new FullyQualified('method_exists'), [new Arg($target), new Arg(new String_($node->name->toString()))]);

        if (ScopeFetcher::fetch($node)->getClassReflection()?->isTrait()) {
            $owner = str_contains($alias, 'infolist') ? InteractsWithInfolists::class : InteractsWithForms::class;
            $isOwner = new FuncCall(new FullyQualified('in_array'), [new Arg(new String_($owner)), new Arg(new FuncCall(new FullyQualified('class_uses_recursive'), [new Arg($target)])), new Arg(new ConstFetch(new Name('true')))]);
            $adapter = new Ternary($isOwner, $adapter, $legacy);
        }

        // Bind the receiver before evaluating arguments; preserve the legacy
        // signature even for unpacking, callables and ignored extra arguments.
        $callable = new FuncCall(new ArrowFunction(['params' => [new Param($component)], 'expr' => new Ternary($condition, $legacy, $adapter)]), [new Arg($receiver)]);

        return $node->isFirstClassCallable() ? $callable : new FuncCall($callable, $node->getArgs());
    }
}
