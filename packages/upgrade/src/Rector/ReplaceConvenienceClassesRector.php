<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;

class ReplaceConvenienceClassesRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall | StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        $factory = $node;
        $calls = [];

        while ($factory instanceof MethodCall || $factory instanceof NullsafeMethodCall) {
            $calls[] = $factory;
            $factory = $factory->var;
        }

        if ((! $factory instanceof StaticCall) || (! $factory->class instanceof Name)
            || (! $factory->name instanceof Identifier) || (! $this->isName($factory->name, 'make'))
            || $factory->isFirstClassCallable() || $factory->getAttribute('legacy_convenience_manual_migration')) {
            return null;
        }

        $replacement = match (strtolower((string) $this->getName($factory->class))) {
            'filament\\actions\\buttonaction' => ['Filament\\Actions\\Action', 'button'],
            'filament\\actions\\iconbuttonaction' => ['Filament\\Actions\\Action', 'iconButton'],
            default => null,
        };

        if (! $replacement) {
            return null;
        }

        $arguments = $factory->args;

        foreach ($calls as $call) {
            array_push($arguments, ...$call->args);
        }

        $nodeFinder = new NodeFinder;
        $requiresReview = $nodeFinder->findFirst($arguments, function (Node $candidate) use ($nodeFinder): bool {
            $type = match (true) {
                $candidate instanceof Param => $candidate->type,
                $candidate instanceof FunctionLike => $candidate->getReturnType(),
                default => null,
            };

            if ($type && $nodeFinder->findFirst([$type], fn (Node $typeNode): bool => $typeNode instanceof Name && $this->isNames($typeNode, [
                'Filament\\Actions\\ButtonAction', 'Filament\\Actions\\IconButtonAction', 'Filament\\Tables\\Filters\\MultiSelectFilter',
            ]))) {
                return true;
            }

            return false;
        });

        if ($requiresReview) {
            $factory->setAttribute('legacy_convenience_manual_migration', true);

            return null;
        }

        $factory->class = new FullyQualified($replacement[0]);
        $configuredFactory = new MethodCall($factory, $replacement[1]);

        if ($calls === []) {
            return $configuredFactory;
        }

        $calls[array_key_last($calls)]->var = $configuredFactory;

        return $node;
    }
}
