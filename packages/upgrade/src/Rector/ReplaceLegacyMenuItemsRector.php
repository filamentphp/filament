<?php

namespace Filament\Upgrade\Rector;

use Filament\Panel;
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ClosureUse;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;

class ReplaceLegacyMenuItemsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /** @param MethodCall | StaticCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable() || count($node->args) !== 1) {
            return null;
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isName($node->class, 'Filament\\Facades\\Filament')) || (! $this->isName($node->name, 'registerUserMenuItems'))) {
                return null;
            }
        } elseif ((! $this->isNames($node->name, ['userMenuItems', 'tenantMenuItems'])) || (! (new ObjectType(Panel::class))->isSuperTypeOf($this->getNativeType($node->var))->yes())) {
            return null;
        }

        $argument = $node->getArgs()[0];

        if ($argument->unpack || ($argument->name && $argument->name->toString() !== 'items') || (! $argument->value instanceof Array_)) {
            return null;
        }

        return $this->migrateItems($argument->value, $this->isName($node->name, 'tenantMenuItems')) ? $node : null;
    }

    private function migrateItems(Array_ $items, bool $tenant): bool
    {
        $changed = false;

        foreach ($items->items as $index => $item) {
            if ($item->unpack || $item->byRef || ($item->key && (! $item->key instanceof String_) && (! $item->key instanceof Int_))) {
                continue;
            }

            if ($item->value instanceof Array_) {
                $changed = $this->migrateItems($item->value, $tenant) || $changed;

                continue;
            }

            $key = $item->key instanceof String_ ? $item->key->value : null;
            $special = in_array($key, $tenant ? ['profile', 'billing', 'register'] : ['account', 'profile', 'logout'], true);
            $replacement = $this->action($item->value, $key, $index, $special);

            if ($replacement) {
                $item->value = $replacement;
                $changed = true;
            }
        }

        return $changed;
    }

    private function action(Expr $expression, ?string $key, int $index, bool $special): ?Expr
    {
        $calls = [];
        $label = null;
        $hasUrl = false;
        $hasPostAction = false;
        $uses = [];
        $hasReferences = false;

        while ($expression instanceof MethodCall) {
            if ((! $expression->name instanceof Identifier) || $expression->isFirstClassCallable()) {
                return null;
            }

            $method = $expression->name->toString();

            if (! in_array($method, ['color', 'icon', 'label', 'sort', 'url', 'postAction', 'hidden', 'visible', 'openUrlInNewTab'], true)) {
                return null;
            }

            foreach ($expression->getArgs() as $argument) {
                if ($argument->unpack || $argument->byRef) {
                    return null;
                }

                // Built-in customizations are deferred. Do not move eager
                // side effects into their configuration callback.
                if ($special && (! $argument->value instanceof Scalar) && (! $argument->value instanceof Variable) && (! $argument->value instanceof Closure) && (! $argument->value instanceof ArrowFunction) && (! $argument->value instanceof Expr\ConstFetch) && (! $argument->value instanceof Expr\ClassConstFetch) && (! ((($argument->value instanceof Expr\UnaryMinus) || ($argument->value instanceof Expr\UnaryPlus)) && ($argument->value->expr instanceof Scalar)))) {
                    return null;
                }

                $value = $argument->value;

                if (($value instanceof Closure) || ($value instanceof ArrowFunction)) {
                    foreach ($value->params as $parameter) {
                        if (($parameter->type instanceof Name) && $this->isName($parameter->type, 'Filament\\Navigation\\MenuItem')) {
                            return null;
                        }
                    }
                }

                $captures = $value instanceof Closure ? $value->uses : [];

                if ($value instanceof Variable && is_string($value->name)) {
                    $captures[] = new ClosureUse($value);
                } elseif ($value instanceof ArrowFunction) {
                    $parameterNames = array_map(static fn (Param $parameter): string => $parameter->var->name, $value->params);

                    foreach ((new NodeFinder)->findInstanceOf($value->expr, Variable::class) as $variable) {
                        if (is_string($variable->name) && (! in_array($variable->name, $parameterNames, true))) {
                            $captures[] = new ClosureUse($variable);
                        }
                    }
                }

                foreach ($captures as $capture) {
                    if ($capture->var->name !== 'this') {
                        if ($special && isset($uses[$capture->var->name]) && ($uses[$capture->var->name]->byRef !== $capture->byRef)) {
                            return null;
                        }

                        $uses[$capture->var->name] = $capture;
                        $hasReferences = $hasReferences || $capture->byRef;
                    }
                }
            }

            if (($method === 'label') && ($label === null) && isset($expression->args[0]) && ($expression->args[0]->value instanceof String_)) {
                $label = $expression->args[0]->value->value;
            }

            $hasUrl = $hasUrl || ($method === 'url');
            $hasPostAction = $hasPostAction || ($method === 'postAction');
            array_unshift($calls, $expression);
            $expression = $expression->var;
        }

        if ((! $expression instanceof StaticCall) || (! $expression->class instanceof Name) || (! $this->isName($expression->class, 'Filament\\Navigation\\MenuItem')) || (! $this->isName($expression->name, 'make')) || $expression->args || $expression->isFirstClassCallable() || ($hasUrl && $hasPostAction)) {
            return null;
        }

        $actionName = isset($uses['action']) ? 'menuAction' : 'action';

        while (isset($uses[$actionName])) {
            $actionName .= 'Value';
        }

        $action = $special ? new Variable($actionName) : new StaticCall(new FullyQualified('Filament\\Actions\\Action'), 'make', [new Arg(new String_($key ?? ($label !== null ? Str::slug(Str::transliterate($label, strict: true)) : 'item-' . ($index + 1))))]);

        foreach ($calls as $call) {
            $method = $call->name->toString();

            if ($method === 'postAction') {
                $arguments = $call->args;

                foreach ($arguments as $argument) {
                    if ($argument->name && $argument->name->toString() === 'action') {
                        $argument->name = new Identifier('url');
                    }
                }

                $action = new MethodCall(new MethodCall($action, 'url', $arguments), 'postToUrl');
            } else {
                $action = new MethodCall($action, $call->name, $call->args);
            }
        }

        if ($special && $hasReferences) {
            return new Closure([
                'params' => [new Param(new Variable($actionName), type: new FullyQualified('Filament\\Actions\\Action'))],
                'uses' => array_values($uses),
                'stmts' => [new Return_($action)],
            ]);
        }

        return $special ? new ArrowFunction([
            'params' => [new Param(new Variable($actionName), type: new FullyQualified('Filament\\Actions\\Action'))],
            'expr' => $action,
        ]) : $action;
    }
}
