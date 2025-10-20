<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceStringPanelParamWithPanelParamRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces the first argument with `Filament::getPanel(...)` in static method calls to `getRouteName()` and `getRouteBaseName()`',
            [
                new CodeSample(
                    'Page::getRouteName(\'admin\');',
                    'Page::getRouteName(Filament\Facades\Filament::getPanel(\'admin\'));'
                ),
            ]
        );
    }

    public function getChanges(): array
    {
        return [
            'getRouteName',
            'getRouteBaseName',
        ];
    }

    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof StaticCall) {
            return null;
        }

        if (! in_array($node->name->name, $this->getChanges())) {
            return null;
        }

        // Only act on static calls with arguments...
        if (! count($node->args)) {
            return null;
        }

        // Wrap first argument into `Filament::getPanel(...)` static call...
        $getPanelStaticCall = new StaticCall(
            new FullyQualified('Filament\Facades\Filament'),
            'getPanel',
            [new Node\Arg($node->args[0]->value)]
        );

        // Replace only first argument...
        $node->args[0] = new Node\Arg($getPanelStaticCall);

        return $node;
    }
}
