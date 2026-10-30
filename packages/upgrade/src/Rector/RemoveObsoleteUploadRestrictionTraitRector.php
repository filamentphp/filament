<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeTraverser;
use Rector\Rector\AbstractRector;

class RemoveObsoleteUploadRestrictionTraitRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [TraitUse::class];
    }

    /** @param TraitUse $node */
    public function refactor(Node $node): Node | int | null
    {
        if ($node->adaptations) {
            return null;
        }

        foreach ($node->traits as $index => $trait) {
            if (! $this->isName($trait, 'Filament\\Schemas\\Concerns\\RestrictsFileUploadsToSchemaComponents')) {
                continue;
            }

            unset($node->traits[$index]);
            $node->traits = array_values($node->traits);

            return $node->traits ? $node : NodeTraverser::REMOVE_NODE;
        }

        return null;
    }
}
