<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
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
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\UnionType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceLegacyPanelIntegrationRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Move global Filament facade integrations to their owning facades', [
            new CodeSample('Filament::registerScriptData($data);', 'FilamentAsset::registerScriptData($data);'),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
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

    private function migratePanelRegistration(StaticCall $call): ?FuncCall
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

            if ($allowedType && (! $allowedType->isSuperTypeOf($this->getNativeType($argument->value))->yes())) {
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
                new Expression(new MethodCall(new StaticCall($call->class, 'getDefaultPanel'), $method, array_map(static fn (string $name): Arg => new Arg(new Variable($name)), $parameters))),
            ], [new Catch_([new FullyQualified('Filament\\Exceptions\\NoDefaultPanelSetException')], new Variable('exception'), [
                new Expression(new Throw_(new New_(new FullyQualified('LogicException'), [new Arg(new String_($message))]))),
            ])])],
        ]), $call->args);
    }
}
