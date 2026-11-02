<?php

namespace Filament\Upgrade\Rector;

use Filament\Panel;
use LogicException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;

class ReplaceLegacyMenuItemsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class, FuncCall::class];
    }

    /** @param MethodCall | StaticCall | FuncCall $node */
    public function refactor(Node $node): ?Node
    {
        // The panel-integration rule may have already expanded the facade call.
        if ($node instanceof FuncCall) {
            if ((! $node->name instanceof Closure) || count($node->name->params) !== 1 || (! $node->name->params[0]->var instanceof Variable) || count($node->args) !== 1 || $node->args[0]->unpack || (! $node->args[0]->value instanceof Array_)) {
                return null;
            }

            $parameterName = $node->name->params[0]->var->name;
            $registration = (new NodeFinder)->findFirst($node->name->stmts, fn (Node $candidate): bool => $candidate instanceof MethodCall && $this->isName($candidate->name, 'userMenuItems') && count($candidate->args) === 1 && $candidate->args[0]->value instanceof Variable && $candidate->args[0]->value->name === $parameterName && $candidate->var instanceof StaticCall && $candidate->var->class instanceof Name && $this->isName($candidate->var->class, 'Filament\\Facades\\Filament') && $this->isName($candidate->var->name, 'getDefaultPanel'));

            return $registration && $this->migrateItems($node->args[0]->value, false) ? $node : null;
        }

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

        foreach ($items->items as $item) {
            if ($item->unpack || $item->byRef || ($item->key && (! $item->key instanceof String_) && (! $item->key instanceof Int_))) {
                continue;
            }

            if ($item->value instanceof Array_) {
                $changed = $this->migrateItems($item->value, $tenant) || $changed;

                continue;
            }

            $calls = $this->configurationCalls($item->value);

            if ($calls === null) {
                continue;
            }

            $key = $item->key instanceof String_ ? $item->key->value : null;
            $special = in_array($key, $tenant ? ['profile', 'billing', 'register'] : ['account', 'profile', 'logout'], true);
            $item->value = $this->actionFactory($calls, $special);
            $changed = true;
        }

        return $changed;
    }

    private function configurationCalls(Expr $expression): ?Array_
    {
        $calls = [];

        while ($expression instanceof MethodCall) {
            if ((! $expression->name instanceof Identifier) || $expression->isFirstClassCallable()) {
                return null;
            }

            array_unshift($calls, $expression);
            $expression = $expression->var;
        }

        if ((! $expression instanceof StaticCall) || (! $expression->class instanceof Name) || (! $this->isName($expression->class, 'Filament\\Navigation\\MenuItem')) || (! $this->isName($expression->name, 'make')) || $expression->args || $expression->isFirstClassCallable()) {
            return null;
        }

        $configuration = [];

        foreach ($calls as $call) {
            $method = $call->name->toString();
            $parameters = match ($method) {
                'color', 'icon', 'label', 'sort' => [$method],
                'postAction' => ['action'],
                'url' => ['url', 'shouldOpenInNewTab'],
                'hidden', 'visible', 'openUrlInNewTab' => ['condition'],
                default => null,
            };

            if ($parameters === null) {
                return null;
            }

            $arguments = [];
            $seen = [];

            foreach ($call->getArgs() as $index => $argument) {
                $parameter = $argument->name?->toString() ?? ($parameters[$index] ?? null);

                if ($argument->unpack || $argument->byRef || (! in_array($parameter, $parameters, true)) || in_array($parameter, $seen, true)) {
                    return null;
                }

                $seen[] = $parameter;
                $arguments[] = new ArrayItem($argument->value, new String_($parameter));
            }

            if ((! in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true)) && (! in_array($parameters[0], $seen, true))) {
                return null;
            }

            $configuration[] = new ArrayItem(new Array_([
                new ArrayItem(new String_($method)),
                new ArrayItem(new Array_($arguments)),
            ]));
        }

        return new Array_($configuration);
    }

    private function actionFactory(Array_ $calls, bool $special): FuncCall
    {
        // Capture argument values immediately, but evaluate configuration closures
        // only when the panel resolves the menu. Do not inject Action utilities
        // into callbacks that previously belonged to a plain support component.
        $code = <<<'PHP'
<?php
(static function (array $calls): \Closure {
    $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
    foreach ($calls as [$method, $arguments]) {
        if ($method === 'url') {
            if (isset($arguments['shouldOpenInNewTab'])) {
                $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
            }
            $configuration['url'] = $arguments['url'];
        } else {
            $parameter = match ($method) {
                'postAction' => 'action',
                'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                default => $method,
            };
            $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
        }
    }
    return static function (ACTION_PARAMETER) use ($configuration): \Filament\Actions\Action {
        $evaluator = new class extends \Filament\Support\Components\Component {};
        $label = $evaluator->evaluate($configuration['label']);
        $postAction = $evaluator->evaluate($configuration['postAction']);
        return ACTION_FACTORY
            ->color($evaluator->evaluate($configuration['color']))
            ->icon($evaluator->evaluate($configuration['icon']))
            ->label($label)
            ->postToUrl(filled($postAction))
            ->sort($evaluator->evaluate($configuration['sort']) ?? 0)
            ->visible((! $evaluator->evaluate($configuration['hidden'])) && $evaluator->evaluate($configuration['visible']))
            ->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
    };
})([]);
PHP;
        $code = str_replace(
            ['ACTION_PARAMETER', 'ACTION_FACTORY'],
            [$special ? '\\Filament\\Actions\\Action $action' : '', $special ? '$action' : '\\Filament\\Actions\\Action::make(\\Illuminate\\Support\\Str::slug(\\Illuminate\\Support\\Str::transliterate($label, strict: true)))'],
            $code,
        );
        $statement = (new ParserFactory)->createForNewestSupportedVersion()->parse($code)[0];

        if ((! $statement instanceof Expression) || (! $statement->expr instanceof FuncCall)) {
            throw new LogicException('The menu action factory must be a function call expression.');
        }

        $factory = $statement->expr;
        $factory->args = [new Arg($calls)];

        return $factory;
    }
}
