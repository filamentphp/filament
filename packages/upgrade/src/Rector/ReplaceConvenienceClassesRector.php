<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceConvenienceClassesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace convenience factories with explicit action styles and multiple select filters', [
            new CodeSample(
                "\\Filament\\Actions\\IconButtonAction::make('edit')",
                "\\Filament\\Actions\\Action::make('edit')->iconButton()",
            ),
            new CodeSample(
                "\\Filament\\Tables\\Filters\\MultiSelectFilter::make('status')->options(['draft' => 'Draft'])",
                "\\Filament\\Tables\\Filters\\SelectFilter::make('status')->multiple()->options(['draft' => 'Draft'])",
            ),
        ]);
    }

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

        if (
            (! $factory instanceof StaticCall) ||
            (! $factory->class instanceof Name) ||
            (! $factory->name instanceof Identifier) ||
            (! $this->isName($factory->name, 'make')) ||
            $factory->isFirstClassCallable() ||
            $factory->getAttribute('convenience_manual_migration')
        ) {
            return null;
        }

        $replacement = match (strtolower((string) $this->getName($factory->class))) {
            'filament\\actions\\buttonaction' => ['Filament\\Actions\\Action', 'button'],
            'filament\\actions\\iconbuttonaction' => ['Filament\\Actions\\Action', 'iconButton'],
            'filament\\tables\\filters\\multiselectfilter' => ['Filament\\Tables\\Filters\\SelectFilter', 'multiple'],
            default => null,
        };

        if (! $replacement) {
            return null;
        }

        $nodeFinder = new NodeFinder;

        // Configuration outside the chain can observe setup order or disable a
        // previously forced mode. Do not broaden legacy registrations to a base.
        if ($nodeFinder->findFirst(
            $this->getFile()->getNewStmts(),
            function (Node $candidate) use ($factory, $nodeFinder): bool {
                if ($candidate instanceof Assign || $candidate instanceof AssignRef) {
                    return (bool) $nodeFinder->findFirst([$candidate->expr], static fn (Node $expression): bool => $expression === $factory);
                }

                return $candidate instanceof StaticCall && $candidate->name instanceof Identifier && $this->isName($candidate->name, 'configureUsing');
            },
        )) {
            $factory->setAttribute('convenience_manual_migration', true);

            return null;
        }

        $arguments = $factory->args;

        foreach ($calls as $call) {
            array_push($arguments, ...$call->args);

            $allowedMethods = ($replacement[1] === 'multiple')
                ? ['label', 'options', 'default', 'relationship', 'searchable', 'preload', 'native', 'placeholder']
                : ['label', 'icon', 'color', 'button', 'iconbutton', 'link', 'view', 'schema', 'modaldescription', 'hiddenlabel'];

            if ($call instanceof NullsafeMethodCall || (! $call->name instanceof Identifier) || $call->isFirstClassCallable() || (! in_array(strtolower($call->name->name), $allowedMethods, true))) {
                $factory->setAttribute('convenience_manual_migration', true);

                return null;
            }
        }

        // A legacy-typed callback cannot accept the canonical component. Also
        // retain forced multiplicity when callbacks configure nested chains.
        if ($nodeFinder->findFirst($arguments, function (Node $candidate) use ($replacement): bool {
            if ($candidate instanceof Name) {
                return in_array(strtolower((string) $this->getName($candidate)), [
                    'filament\\actions\\buttonaction',
                    'filament\\actions\\iconbuttonaction',
                    'filament\\tables\\filters\\multiselectfilter',
                ], true);
            }

            return ($replacement[1] === 'multiple') && ($candidate instanceof MethodCall || $candidate instanceof NullsafeMethodCall) && ((! $candidate->name instanceof Identifier) || $this->isName($candidate->name, 'multiple'));
        })) {
            $factory->setAttribute('convenience_manual_migration', true);

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
