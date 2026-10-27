<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RemoveObsoleteUploadRestrictionTraitRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove the redundant upload restriction trait from schema consumers without adaptations', [
            new CodeSample(
                <<<'PHP'
                    class Form extends \Livewire\Component
                    {
                        use \Filament\Schemas\Concerns\InteractsWithSchemas;
                        use \Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
                    }
                    PHP,
                <<<'PHP'
                    class Form extends \Livewire\Component
                    {
                        use \Filament\Schemas\Concerns\InteractsWithSchemas;
                    }
                    PHP,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $usesSchemas = false;

        foreach ($node->getTraitUses() as $traitUse) {
            // Adaptations can alias the removed method or resolve a conflict.
            // Leave the whole class for review rather than invalidating either.
            if ($traitUse->adaptations !== []) {
                return null;
            }

            foreach ($traitUse->traits as $trait) {
                $usesSchemas = $usesSchemas || $this->isName($trait, 'Filament\\Schemas\\Concerns\\InteractsWithSchemas');
            }
        }

        if (! $usesSchemas) {
            return null;
        }

        $changed = false;

        foreach ($node->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $index => $trait) {
                if ($this->isName($trait, 'Filament\\Schemas\\Concerns\\RestrictsFileUploadsToSchemaComponents')) {
                    unset($traitUse->traits[$index]);
                    $changed = true;
                }
            }

            $traitUse->traits = array_values($traitUse->traits);

            if ($traitUse->traits === []) {
                foreach ($node->stmts as $index => $statement) {
                    if ($statement === $traitUse) {
                        unset($node->stmts[$index]);
                    }
                }
            }
        }

        return $changed ? $node : null;
    }
}
