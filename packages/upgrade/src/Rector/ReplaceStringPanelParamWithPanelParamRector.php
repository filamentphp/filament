<?php

namespace Filament\Upgrade\Rector;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\Node\AttributeKey;
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
        if ((! $node instanceof StaticCall) || (! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        if (! in_array($node->name->name, $this->getChanges())) {
            return null;
        }

        $owner = match ($node->name->name) {
            'getRouteName' => Page::class,
            'getRouteBaseName' => Resource::class,
            default => null,
        };

        if ($owner === null) {
            return null;
        }

        $scope = $node->getAttribute(AttributeKey::SCOPE);
        $receiverType = (($node->class instanceof Name) && ($scope instanceof Scope))
            ? $scope->resolveTypeByName($node->class)
            : $this->getType($node->class);

        if ($receiverType->isClassString()->yes()) {
            $receiverType = $receiverType->getClassStringObjectType();
        }

        if (! (new ObjectType($owner))->isSuperTypeOf($receiverType)->yes()) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $argument = $node->args[0];

        if ($argument->unpack || ($argument->name && (! $this->isName($argument->name, 'panel'))) || (! $this->getType($argument->value)->isString()->yes())) {
            return null;
        }

        // Wrap first argument into `Filament::getPanel(...)` static call...
        $getPanelStaticCall = new StaticCall(
            new FullyQualified('Filament\Facades\Filament'),
            'getPanel',
            [new Node\Arg($argument->value)]
        );

        // Replace only first argument...
        $argument->value = $getPanelStaticCall;

        return $node;
    }
}
