<?php

namespace Filament\Upgrade\Rector;

use Filament\Pages\Page;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Panel;
use Filament\Resources\Resource;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
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
                    'Page::prependClusterSlug($slug);',
                    'Page::prependClusterSlug(Filament\Facades\Filament::getCurrentOrDefaultPanel(), $slug);'
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
        if ((! $node instanceof StaticCall) || (! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        if (! in_array($node->name->name, $this->getChanges())) {
            return null;
        }

        $scope = $node->getAttribute(AttributeKey::SCOPE);
        $receiverType = (($node->class instanceof Name) && ($scope instanceof Scope))
            ? $scope->resolveTypeByName($node->class)
            : $this->getType($node->class);

        if ($receiverType->isClassString()->yes()) {
            $receiverType = $receiverType->getClassStringObjectType();
        }

        $ownerType = match ($node->name->name) {
            'getRoutePath' => TypeCombinator::union(new ObjectType(Page::class), new ObjectType(RegisterTenant::class)),
            'getRelativeRouteName' => TypeCombinator::union(new ObjectType(Page::class), new ObjectType(RegisterTenant::class), new ObjectType(Resource::class)),
            'getRoutePrefix' => new ObjectType(Resource::class),
            'prependClusterRouteBaseName', 'prependClusterSlug' => new ObjectType(Page::class),
            default => null,
        };

        if (($ownerType === null) || (! $ownerType->isSuperTypeOf($receiverType)->yes())) {
            return null;
        }

        $argumentName = match ($node->name->name) {
            'prependClusterRouteBaseName' => 'name',
            'prependClusterSlug' => 'slug',
            default => null,
        };

        // Only migrate the old signature, not calls that already pass a panel.
        if (count($node->args) !== (($argumentName === null) ? 0 : 1)) {
            return null;
        }

        if ($argumentName !== null) {
            $argument = $node->args[0];

            if ($argument->unpack || ($argument->name && (! $this->isName($argument->name, $argumentName))) || (! $this->getType($argument->value)->isString()->yes())) {
                return null;
            }
        }

        $isPanelVariableDefinedInScope = false;

        if ($scope instanceof Scope) {
            $isPanelVariableDefinedInScope = in_array('panel', $scope->getDefinedVariables())
                && (new ObjectType(Panel::class))->isSuperTypeOf($scope->getVariableType('panel'))->yes();
        }

        if ($isPanelVariableDefinedInScope) {
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
