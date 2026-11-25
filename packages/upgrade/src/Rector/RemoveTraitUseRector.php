<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RemoveTraitUseRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string>
     */
    protected array $traitsToRemove = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove specific traits from code', []);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    /**
     * @param  Class_ | Trait_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;

        foreach ($node->stmts as $statementKey => $statement) {
            if (! ($statement instanceof TraitUse)) {
                continue;
            }

            foreach ($statement->traits as $traitKey => $trait) {
                if (! $this->isNames($trait, $this->traitsToRemove)) {
                    continue;
                }

                unset($statement->traits[$traitKey]);

                $hasChanged = true;
            }

            if ($statement->traits === []) {
                unset($node->stmts[$statementKey]);
            }
        }

        return $hasChanged ? $node : null;
    }

    /**
     * @param  array<string>  $configuration
     */
    public function configure(array $configuration): void
    {
        $this->traitsToRemove = $configuration;
    }
}
