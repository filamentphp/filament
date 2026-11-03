<?php

namespace Filament\Upgrade\Rector;

use Filament\FilamentManager;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ClosureUse;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\UnionType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class ReplaceLegacyPanelIntegrationRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [StaticCall::class, MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param  StaticCall | MethodCall | NullsafeMethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof StaticCall) {
            return $this->migrateManagerCall($node);
        }

        if ((! $node->class instanceof Name) || (! $this->isName($node->class, 'Filament\\Facades\\Filament')) || (! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        if ($registration = $this->migratePanelRegistration($node)) {
            return $registration;
        }

        $method = strtolower($node->name->toString());
        $parameters = match ($method) {
            'registerrenderhook' => ['name', 'hook'],
            'renderhook' => ['name'],
            'registerscriptdata' => ['data'],
            default => null,
        };

        if (($parameters === null) || (count($node->getArgs()) !== count($parameters))) {
            return null;
        }

        $seenParameters = [];

        foreach ($node->getArgs() as $index => $argument) {
            $parameter = $argument->name?->toString() ?? $parameters[$index];

            // Do not start consuming extra arguments that the old wrappers ignored.
            if ($argument->unpack || $argument->byRef || (! in_array($parameter, $parameters, true)) || in_array($parameter, $seenParameters, true)) {
                return null;
            }

            $seenParameters[] = $parameter;
        }

        return new StaticCall(
            new FullyQualified(($method === 'registerscriptdata') ? 'Filament\\Support\\Facades\\FilamentAsset' : 'Filament\\Support\\Facades\\FilamentView'),
            match ($method) {
                'registerrenderhook' => 'registerRenderHook',
                'registerscriptdata' => 'registerScriptData',
                default => 'renderHook',
            },
            $node->args,
        );
    }

    private function migrateManagerCall(MethodCall | NullsafeMethodCall $call): ?Node
    {
        if ((! $call->name instanceof Identifier) || $call->isFirstClassCallable()) {
            return null;
        }

        $receiverType = TypeCombinator::removeNull($this->getType($call->var));

        if (! (new ObjectType(FilamentManager::class))->isSuperTypeOf($receiverType)->yes()) {
            return null;
        }

        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            if ($classReflection->hasNativeMethod($call->name->toString()) && ($classReflection->getNativeMethod($call->name->toString())->getDeclaringClass()->getName() !== FilamentManager::class)) {
                return null;
            }
        }

        $manager = new Variable('manager');
        $facadeCall = new StaticCall(new FullyQualified('Filament\\Facades\\Filament'), $call->name, $call->args);
        $registration = $this->migratePanelRegistration($facadeCall, $manager);

        if ($registration) {
            $closure = $registration->name;
        } else {
            $parameters = match (strtolower($call->name->toString())) {
                'registerrenderhook' => [new Param(new Variable('name'), type: new Identifier('string')), new Param(new Variable('hook'), type: new FullyQualified('Closure'))],
                'renderhook' => [new Param(new Variable('name'), type: new Identifier('string'))],
                'registerscriptdata' => [new Param(new Variable('data'), type: new Identifier('array'))],
                default => null,
            };
            $replacement = $parameters ? $this->refactor($facadeCall) : null;

            if (! $replacement instanceof StaticCall) {
                return null;
            }

            $replacement->args = array_map(static fn (Param $parameter): Arg => new Arg($parameter->var), $parameters);
            $closure = new Closure([
                'static' => true,
                'params' => $parameters,
                'returnType' => $this->isName($call->name, 'renderHook') ? new FullyQualified('Illuminate\\Contracts\\Support\\Htmlable') : new Identifier('void'),
                'stmts' => [$this->isName($call->name, 'renderHook') ? new Return_($replacement) : new Expression($replacement)],
            ]);
        }

        if (! $closure instanceof Closure) {
            return null;
        }

        $closure->uses = [new ClosureUse($manager)];
        // A base-typed manager may resolve to an application override at runtime.
        $callable = new Ternary(
            new FuncCall(new FullyQualified('method_exists'), [new Arg($manager), new Arg(new String_($call->name->toString()))]),
            new StaticCall(new FullyQualified('Closure'), 'fromCallable', [new Arg(new Array_([new ArrayItem($manager), new ArrayItem(new String_($call->name->toString()))]))]),
            $closure,
        );

        $receiver = $call->var;
        $accesses = [];

        while (($receiver instanceof MethodCall) || ($receiver instanceof NullsafeMethodCall) || ($receiver instanceof PropertyFetch) || ($receiver instanceof NullsafePropertyFetch) || ($receiver instanceof ArrayDimFetch)) {
            $accesses[] = $receiver;
            $receiver = $receiver->var;
        }

        $conditions = [];

        // Hoist earlier nullsafe boundaries without confusing a skipped access
        // with a non-null owner's getter that actually returns `null`.
        foreach (array_reverse($accesses) as $index => $access) {
            if (($access instanceof NullsafeMethodCall) || ($access instanceof NullsafePropertyFetch)) {
                $variableName = 'filamentUpgradeReceiver' . $index;

                while (str_contains($this->getFile()->getFileContent(), '$' . $variableName)) {
                    $variableName .= 'Value';
                }

                $temporaryReceiver = new Variable($variableName);
                $conditions[] = new Identical(new Assign($temporaryReceiver, $receiver), new ConstFetch(new Name('null')));
                $receiver = $temporaryReceiver;
            }

            $receiver = match (true) {
                ($access instanceof MethodCall) || ($access instanceof NullsafeMethodCall) => new MethodCall($receiver, $access->name, $access->args),
                ($access instanceof PropertyFetch) || ($access instanceof NullsafePropertyFetch) => new PropertyFetch($receiver, $access->name),
                default => new ArrayDimFetch($receiver, $access->dim),
            };
        }

        $isNullsafe = ($call instanceof NullsafeMethodCall) || $conditions;

        if ($isNullsafe) {
            // `Closure::__invoke()` coerces scalars even in strict files. Keep
            // its nullsafe boundary, but validate through a direct callable call.
            $callback = new Variable('callback');
            $arguments = new Variable('arguments');
            $callable = new FuncCall(new ArrowFunction([
                'static' => true,
                'params' => [new Param($callback, type: new Identifier('callable'))],
                'expr' => new ArrowFunction([
                    'static' => true,
                    'params' => [new Param($arguments, variadic: true)],
                    'expr' => new FuncCall($callback, [new Arg($arguments, unpack: true)]),
                ]),
            ]), [new Arg($callable)]);
        }

        // Build the callable before evaluating arguments, preserving nullsafe
        // argument and subsequent-chain short circuit semantics.
        $factory = new FuncCall(new ArrowFunction([
            'static' => true,
            'params' => [new Param($manager, type: new NullableType(new FullyQualified(FilamentManager::class)))],
            'expr' => new Ternary(
                new Identical($manager, new ConstFetch(new Name('null'))),
                $conditions && (! $call instanceof NullsafeMethodCall)
                    ? new Throw_(new New_(new FullyQualified('Error'), [new Arg(new String_('Call to a member function ' . $call->name->toString() . '() on null'))]))
                    : new ConstFetch(new Name('null')),
                $callable,
            ),
        ]), [new Arg($receiver)]);

        foreach (array_reverse($conditions) as $condition) {
            $factory = new Ternary($condition, new ConstFetch(new Name('null')), $factory);
        }

        return $isNullsafe
            ? new NullsafeMethodCall($factory, '__invoke', $call->args)
            : new FuncCall($factory, $call->args);
    }

    private function migratePanelRegistration(StaticCall $call, ?Variable $manager = null): ?FuncCall
    {
        $configuration = match (strtolower($call->name->toString())) {
            'registerpages' => ['pages', 'pages', 'Please use the `pages()` method on the panel configuration to register pages.'],
            'registerresources' => ['resources', 'resources', 'Please use the `resources()` method on the panel configuration to register resources.'],
            'registerwidgets' => ['widgets', 'widgets', 'Please use the `widgets()` method on the panel configuration to register widgets.'],
            'registernavigationgroups' => ['navigationGroups', 'groups', 'Please use the `navigationGroups()` method on the panel configuration to register navigation groups. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-navigation-groups'],
            'registernavigationitems' => ['navigationItems', 'items', 'Please use the `navigationItems()` method on the panel configuration to register navigation items. See the documentation - https://filamentphp.com/docs/panels/navigation#registering-custom-navigation-items'],
            'registerusermenuitems' => ['userMenuItems', 'items', 'Please use the `userMenuItems()` method on the panel configuration to register user menu items. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-the-user-menu'],
            'registertheme' => ['theme', 'theme', 'Please use the `theme()` method on the panel configuration to register themes.'],
            'registervitetheme' => ['viteTheme', 'theme', 'Please use the `viteTheme()` method on the panel configuration to register themes.'],
            default => null,
        };

        if (! $configuration) {
            return null;
        }

        [$method, $parameter, $message] = $configuration;
        $parameters = [$parameter, ...($method === 'viteTheme' ? ['buildDirectory'] : [])];
        $seenParameters = [];

        foreach ($call->getArgs() as $index => $argument) {
            $name = $argument->name?->toString() ?? ($parameters[$index] ?? null);

            if ($argument->unpack || $argument->byRef || (! in_array($name, $parameters, true)) || in_array($name, $seenParameters, true)) {
                return null;
            }

            // Facade dispatch entered the old weakly typed manager method.
            // Do not introduce strict scalar validation in a strict caller.
            $allowedType = match (true) {
                $name === 'buildDirectory' => TypeCombinator::union(new StringType, new NullType),
                $method === 'theme' => TypeCombinator::union(new StringType, new ObjectType('Illuminate\\Contracts\\Support\\Htmlable'), new NullType),
                $method === 'viteTheme' => TypeCombinator::union(new StringType, new ArrayType(new MixedType, new MixedType)),
                default => null,
            };

            if ((! $manager) && $allowedType && (! $allowedType->isSuperTypeOf($this->getNativeType($argument->value))->yes())) {
                return null;
            }

            $seenParameters[] = $name;
        }

        if (! in_array($parameter, $seenParameters, true)) {
            return null;
        }

        $type = match ($method) {
            'theme' => new UnionType([new Identifier('string'), new FullyQualified('Illuminate\\Contracts\\Support\\Htmlable'), new Identifier('null')]),
            'viteTheme' => new UnionType([new Identifier('string'), new Identifier('array')]),
            default => new Identifier('array'),
        };
        $closureParameters = [new Param(new Variable($parameter), type: $type)];

        if ($method === 'viteTheme') {
            $closureParameters[] = new Param(new Variable('buildDirectory'), default: new ConstFetch(new Name('null')), type: new NullableType(new Identifier('string')));
        }

        // Evaluate and validate arguments once before panel lookup. An immediate
        // closure preserves argument lifetime and void results without new locals.
        return new FuncCall(new Closure([
            'static' => true,
            'params' => $closureParameters,
            'returnType' => new Identifier('void'),
            'stmts' => [new TryCatch([
                new Expression(new MethodCall($manager ? new MethodCall($manager, 'getDefaultPanel') : new StaticCall($call->class, 'getDefaultPanel'), $method, array_map(static fn (string $name): Arg => new Arg(new Variable($name)), $parameters))),
            ], [new Catch_([new FullyQualified('Filament\\Exceptions\\NoDefaultPanelSetException')], new Variable('exception'), [
                new Expression(new Throw_(new New_(new FullyQualified('LogicException'), [new Arg(new String_($message))]))),
            ])])],
        ]), $call->args);
    }
}
