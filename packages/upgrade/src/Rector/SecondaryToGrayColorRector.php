<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class SecondaryToGrayColorRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        /** @var MethodCall $node */
        if (! in_array($node->name->name, ['color', 'iconColor', 'hintColor', 'offColor', 'onColor', 'falseColor', 'trueColor'])) {
            return null;
        }

        $argument = $node->getArgs()[0]->value;

        if (! ($argument instanceof Node\Scalar\String_)) {
            return null;
        }

        if ($argument->value !== 'secondary') {
            return null;
        }

        $argument->value = 'gray';

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change all references to the `secondary` color to `gray`',
            [
                new CodeSample(
                    'color(\'secondary\')',
                    'color(\'gray\')',
                ),
            ]
        );
    }
}
