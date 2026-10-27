<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedConstantsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename deprecated constants on their exact owning classes', [
            new CodeSample(
                '\Filament\Support\Enums\IconSize::ExtraExtraLarge',
                '\Filament\Support\Enums\IconSize::TwoExtraLarge',
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    /**
     * @param  ClassConstFetch  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->class instanceof Name) || (! $node->name instanceof Identifier)) {
            return null;
        }

        // A subtype can override either constant with a different value.
        $renames = match (strtolower((string) $this->getName($node->class))) {
            'filament\\support\\enums\\iconsize' => ['ExtraExtraLarge' => 'TwoExtraLarge'],
            'filament\\tables\\view\\tablesrenderhook' => [
                'TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER' => 'TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER',
                'TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE' => 'TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE',
            ],
            default => [],
        };

        $replacement = $renames[$node->name->name] ?? null;

        if (! $replacement) {
            return null;
        }

        $node->name = new Identifier($replacement);

        return $node;
    }
}
