<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class FixGetSetClosureTypesRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ArrowFunction::class, Closure::class];
    }

    public function refactor(Node $node): ?Node
    {
        /** @var ArrowFunction | Closure $node */
        $params = $node->params;

        foreach ($params as $param) {
            if (! $param->type) {
                continue;
            }

            if (($param->var->name === 'get') && ($this->isName($param->type, 'Closure') || $this->isName($param->type, '\\Closure'))) {
                $param->type = new Name('\\Filament\\Forms\\Get');
            }

            if (($param->var->name === 'set') && ($this->isName($param->type, 'Closure') || $this->isName($param->type, '\\Closure'))) {
                $param->type = new Name('\\Filament\\Forms\\Set');
            }
        }

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fix the types for `$get` and `$set` closure arguments',
            [
                new CodeSample(
                    'function (Closure $get, Closure $set)',
                    'function (\\Filament\\Forms\\Get $get, \\Filament\\Forms\\Set $set)',
                ),
            ]
        );
    }
}
