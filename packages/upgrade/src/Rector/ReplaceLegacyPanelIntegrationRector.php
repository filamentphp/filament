<?php

namespace Filament\Upgrade\Rector;

use Filament\FilamentManager;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class ReplaceLegacyPanelIntegrationRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [StaticCall::class, MethodCall::class, NullsafeMethodCall::class];
    }

    /** @param StaticCall | MethodCall | NullsafeMethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isName($node->class, 'Filament\\Facades\\Filament'))) {
                return null;
            }
        } else {
            $type = TypeCombinator::removeNull($this->getType($node->var));

            if (! (new ObjectType(FilamentManager::class))->isSuperTypeOf($type)->yes()) {
                return null;
            }

            foreach ($type->getObjectClassReflections() as $reflection) {
                if ($reflection->hasNativeMethod($node->name->toString()) && ($reflection->getNativeMethod($node->name->toString())->getDeclaringClass()->getName() !== FilamentManager::class)) {
                    return null;
                }
            }
        }

        $configuration = match (strtolower($node->name->toString())) {
            'registerpages' => ['pages', ['pages']],
            'registerresources' => ['resources', ['resources']],
            'registerwidgets' => ['widgets', ['widgets']],
            'registernavigationgroups' => ['navigationGroups', ['groups']],
            'registernavigationitems' => ['navigationItems', ['items']],
            'registerusermenuitems' => ['userMenuItems', ['items']],
            'registertheme' => ['theme', ['theme']],
            'registervitetheme' => ['viteTheme', ['theme', 'buildDirectory']],
            'registerrenderhook' => ['registerRenderHook', ['name', 'hook'], 'Filament\\Support\\Facades\\FilamentView'],
            'renderhook' => ['renderHook', ['name'], 'Filament\\Support\\Facades\\FilamentView'],
            'registerscriptdata' => ['registerScriptData', ['data'], 'Filament\\Support\\Facades\\FilamentAsset'],
            default => null,
        };

        if (! $configuration) {
            return null;
        }

        [$method, $parameters] = $configuration;
        $seen = [];

        foreach ($node->getArgs() as $index => $argument) {
            $parameter = $argument->name?->toString() ?? ($parameters[$index] ?? null);

            if ($argument->unpack || $argument->byRef || (! in_array($parameter, $parameters, true)) || in_array($parameter, $seen, true)) {
                return null;
            }

            $seen[] = $parameter;
        }

        if (array_diff(array_filter($parameters, static fn (string $parameter): bool => $parameter !== 'buildDirectory'), $seen)) {
            return null;
        }

        if (isset($configuration[2])) {
            // A facade rewrite cannot retain a meaningful manager receiver or a
            // nullsafe chain. Leave those integrations for an explicit migration.
            if (! $node instanceof StaticCall) {
                $receiver = $node->var;
                $isHelper = ($receiver instanceof FuncCall) && ($receiver->name instanceof Name) && $this->isName($receiver->name, 'filament') && (! $receiver->args);

                if (($node instanceof NullsafeMethodCall) || ((! $receiver instanceof Variable) && (! $isHelper))) {
                    return null;
                }
            }

            return new StaticCall(new FullyQualified($configuration[2]), $method, $node->args);
        }

        $panel = $node instanceof StaticCall
            ? new StaticCall($node->class, 'getDefaultPanel')
            : (($node instanceof NullsafeMethodCall)
                ? new NullsafeMethodCall($node->var, 'getDefaultPanel')
                : new MethodCall($node->var, 'getDefaultPanel'));

        return new MethodCall($panel, $method, $node->args);
    }
}
