<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddPanelParamToRouteMethodsRector extends AbstractRector
{
    public function __construct(
        protected BetterNodeFinder $betterNodeFinder
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Prepend `Filament::getCurrentOrDefaultPanel()` or `$panel` to static method calls to `getRoutePath()`, `getRelativeRouteName()`, etc.',
            [
                new CodeSample(
                    'Page::getRoutePath();',
                    'Page::getRoutePath(Filament\Facades\Filament::getCurrentOrDefaultPanel());'
                ),
                new CodeSample(
                    'Page::getRoutePath($param1, $param2);',
                    'Page::getRoutePath(Filament\Facades\Filament::getCurrentOrDefaultPanel(), $param1, $param2);'
                ),
            ]
        );
    }

    public function getChanges(): array
    {
        return [
            'getRoutePath',
            'getRelativeRouteName',
            'getRoutePrefix',
            'prependClusterRouteBaseName',
            'prependClusterSlug',
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

        $scope = $node->getAttribute(AttributeKey::SCOPE);

        $isPanelVariableDefinedInScope = false;

        if ($scope instanceof Scope) {
            $isPanelVariableDefinedInScope = in_array('panel', $scope->getDefinedVariables());
        }

        if ($isPanelVariableDefinedInScope) {
            // Assuming `$panel` variable always refers to a panel in Filament-context...
            $panelArg = new Node\Arg(new Variable('panel'));
        } else {
            $getCurrentOrDefaultPanelStaticCall = new StaticCall(new FullyQualified('Filament\Facades\Filament'), 'getCurrentOrDefaultPanel');

            $panelArg = new Node\Arg($getCurrentOrDefaultPanelStaticCall);
        }

        // Prepend new argument...
        array_unshift($node->args, $panelArg);

        return $node;
    }
}
