<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

class ReplaceExposedLivewireMethodAttributeRector extends AbstractRector
{
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

        $legacyAttribute->name = new FullyQualified('Filament\\Support\\Components\\Attributes\\Exposed');

        return $node;
    }
}
