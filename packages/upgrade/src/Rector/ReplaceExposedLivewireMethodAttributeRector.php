<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UseItem;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceExposedLivewireMethodAttributeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace the deprecated exposure attribute without broadening type references', [
            new CodeSample(
                '#[\\Filament\\Support\\Components\\Attributes\\ExposedLivewireMethod] public function save() {}',
                '#[\\Filament\\Support\\Components\\Attributes\\Exposed] public function save() {}',
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param  ClassMethod  $node
     */
    public function refactor(Node $node): ?Node
    {
        $legacyAttribute = null;
        $exposureAttributeCount = 0;

        foreach ($node->attrGroups as $group) {
            foreach ($group->attrs as $attribute) {
                if ($this->isName($attribute->name, 'Filament\\Support\\Components\\Attributes\\ExposedLivewireMethod')) {
                    $legacyAttribute = $attribute;
                    $exposureAttributeCount++;
                } elseif ($this->isName($attribute->name, 'Filament\\Support\\Components\\Attributes\\Exposed')) {
                    $exposureAttributeCount++;
                }
            }
        }

        // Neither attribute is repeatable. Combining the old and new names
        // requires review rather than creating an invalid duplicate attribute.
        if (($legacyAttribute === null) || ($exposureAttributeCount !== 1)) {
            return null;
        }

        $nodeFinder = new NodeFinder;
        $attributeAndImportNames = [];

        foreach ([...$nodeFinder->findInstanceOf($this->getFile()->getNewStmts(), Attribute::class), ...$nodeFinder->findInstanceOf($this->getFile()->getNewStmts(), UseItem::class)] as $candidate) {
            $attributeAndImportNames[] = $candidate->name;
        }

        // Keep declarations, type checks and reflection references coordinated.
        // Broadening a legacy type to `Exposed` is not an attribute rename.
        if ($nodeFinder->findFirst($this->getFile()->getNewStmts(), function (Node $candidate) use ($attributeAndImportNames): bool {
            if ($candidate instanceof String_) {
                return strtolower(ltrim($candidate->value, '\\')) === 'filament\\support\\components\\attributes\\exposedlivewiremethod';
            }

            return ($candidate instanceof Name) &&
                $this->isName($candidate, 'Filament\\Support\\Components\\Attributes\\ExposedLivewireMethod') &&
                (! in_array($candidate, $attributeAndImportNames, true));
        })) {
            return null;
        }

        $legacyAttribute->name = new FullyQualified('Filament\\Support\\Components\\Attributes\\Exposed');

        return $node;
    }
}
