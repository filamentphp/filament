<?php

namespace Filament\Upgrade\Rector;

use Filament\Schemas\Components\Component;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RemoveObsoleteConcealmentRector extends AbstractRemovedGetterRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Lower the removed concealment getter without changing marker contracts', [new CodeSample('$component->isConcealed()', 'false')]);
    }

    protected function owner(): string
    {
        return Component::class;
    }

    protected function method(): string
    {
        return 'isConcealed';
    }

    protected function result(Expr $receiver): Expr
    {
        return new ConstFetch(new Name('false'));
    }
}
