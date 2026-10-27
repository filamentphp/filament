<?php

namespace Filament\Upgrade\Rector;

use Filament\Panel;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceLegacyMenuItemsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert literal menu-item factories in panel menu registrations to action closures', [
            new CodeSample("\$panel->userMenuItems([MenuItem::make()->label('Help')]);", "\$panel->userMenuItems([static fn (): Action => Action::make('help')->color(null)->icon(null)->label('Help')->postToUrl(false)->sort(0)->visible(true)->url(null, false)]);"),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /** @param MethodCall | StaticCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable() || (count($node->getArgs()) !== 1)) {
            return null;
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isName($node->class, 'Filament\\Facades\\Filament')) || (! $this->isName($node->name, 'registerUserMenuItems'))) {
                return null;
            }
        } elseif ((! $this->isNames($node->name, ['userMenuItems', 'tenantMenuItems'])) || (! $this->getNativeType($node->var)->equals(new ObjectType(Panel::class)))) {
            return null;
        }

        $argument = $node->getArgs()[0];

        if ($argument->unpack || ($argument->name && ($argument->name->toString() !== 'items')) || (! $argument->value instanceof Array_)) {
            return null;
        }

        // Legacy global configuration needs an application-owned factory,
        // not a broad re-registration against every modern action.
        if ((new NodeFinder)->findFirst($this->getFile()->getNewStmts(), fn (Node $candidate): bool => $candidate instanceof StaticCall && $candidate->class instanceof Name && $this->isName($candidate->class, 'Filament\\Navigation\\MenuItem') && (! $this->isName($candidate->name, 'make')))) {
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

            $values = $this->literalConfiguration($item->value);

            if ($values === null) {
                continue;
            }

            $key = $item->key instanceof String_ ? $item->key->value : null;
            $special = in_array($key, $tenant ? ['profile', 'billing', 'register'] : ['account', 'profile', 'logout'], true);
            $action = $special ? new Variable('action') : new StaticCall(new FullyQualified('Filament\\Actions\\Action'), 'make', [new Arg(new StaticCall(new FullyQualified('Illuminate\\Support\\Str'), 'slug', [new Arg(new StaticCall(new FullyQualified('Illuminate\\Support\\Str'), 'transliterate', [new Arg(new String_($values['label'] ?? '')), new Arg($this->boolean(true), name: new Identifier('strict'))]))]))]);

            foreach ([
                'color' => [new String_($values['color'] ?? '')],
                'icon' => [new String_($values['icon'] ?? '')],
                'label' => [new String_($values['label'] ?? '')],
            ] as $method => $arguments) {
                $action = new MethodCall($action, $method, [new Arg(isset($values[$method]) ? $arguments[0] : new ConstFetch(new Name('null')))]);
            }

            $action = new MethodCall($action, 'postToUrl', [new Arg($this->boolean(trim($values['postAction'] ?? '') !== ''))]);
            $action = new MethodCall($action, 'sort', [new Arg(new Int_($values['sort'] ?? 0))]);
            $action = new MethodCall($action, 'visible', [new Arg($this->boolean((! ($values['hidden'] ?? false)) && ($values['visible'] ?? true)))]);
            $url = $values['url'] ?? $values['postAction'] ?? null;
            $action = new MethodCall($action, 'url', [new Arg($url === null ? new ConstFetch(new Name('null')) : new String_($url)), new Arg($this->boolean($values['openUrlInNewTab'] ?? false))]);
            $item->value = new ArrowFunction([
                'static' => true,
                'params' => $special ? [new Param(new Variable('action'), type: new FullyQualified('Filament\\Actions\\Action'))] : [],
                'returnType' => new FullyQualified('Filament\\Actions\\Action'),
                'expr' => $action,
            ]);
            $changed = true;
        }

        return $changed;
    }

    /** @return array<string, bool | int | string | null> | null */
    private function literalConfiguration(Expr $expression): ?array
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

        $values = [];

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

            foreach ($call->getArgs() as $index => $argument) {
                $name = $argument->name?->toString() ?? ($parameters[$index] ?? null);
                $value = $argument->value;

                if ($argument->unpack || $argument->byRef || (! in_array($name, $parameters, true)) || array_key_exists($name, $arguments)) {
                    return null;
                }

                $literal = $value instanceof ConstFetch ? strtolower($value->name->toString()) : null;
                $boolean = ($name === 'condition') || ($name === 'shouldOpenInNewTab');
                $signedInteger = (($value instanceof Expr\UnaryMinus) || ($value instanceof Expr\UnaryPlus)) && ($value->expr instanceof Int_);

                if ($boolean ? (! in_array($literal, $name === 'condition' ? ['true', 'false'] : ['true', 'false', 'null'], true)) : (($method === 'sort') ? ((! $value instanceof Int_) && (! $signedInteger) && ($literal !== 'null')) : ((! $value instanceof String_) && ($literal !== 'null')))) {
                    return null;
                }

                $arguments[$name] = match (true) {
                    $value instanceof String_, $value instanceof Int_ => $value->value,
                    $signedInteger => $value instanceof Expr\UnaryMinus ? -$value->expr->value : $value->expr->value,
                    $literal === 'true' => true,
                    $literal === 'false' => false,
                    default => null,
                };
            }

            if ((! in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true)) && (! array_key_exists($parameters[0], $arguments))) {
                return null;
            }

            $values[$method] = $arguments[$parameters[0]] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);

            if (($method === 'url') && isset($arguments['shouldOpenInNewTab'])) {
                $values['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
            }
        }

        return $values;
    }

    private function boolean(bool $value): ConstFetch
    {
        return new ConstFetch(new Name($value ? 'true' : 'false'));
    }
}
