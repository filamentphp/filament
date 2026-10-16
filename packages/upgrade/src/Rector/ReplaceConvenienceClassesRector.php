<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\UnionType;
use Rector\Configuration\RenamedClassesDataCollector;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceConvenienceClassesRector extends AbstractRector
{
    public function __construct(
        private RenamedClassesDataCollector $renamedClassesDataCollector,
    ) {}

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
        return [StaticCall::class, UnionType::class];
    }

    /**
     * @param  StaticCall | UnionType  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof UnionType) {
            $renamedClasses = $this->renamedClassesDataCollector->getOldToNewClasses();
            $types = [];

            foreach ($node->types as $type) {
                if ($type instanceof Name) {
                    $className = $this->getName($type);
                    $types[$renamedClasses[$className] ?? $className] ??= $type;
                } else {
                    $types[] = $type;
                }
            }

            if (count($types) === count($node->types)) {
                return null;
            }

            $node->types = array_values($types);

            return count($types) === 1 ? $node->types[0] : $node;
        }

        if (
            (! $node->class instanceof Name) ||
            (! $node->name instanceof Identifier) ||
            (! $this->isName($node->name, 'make')) ||
            $node->isFirstClassCallable()
        ) {
            return null;
        }

        $replacement = match ($this->getName($node->class)) {
            'Filament\\Actions\\ButtonAction' => ['Filament\\Actions\\Action', 'button'],
            'Filament\\Actions\\IconButtonAction' => ['Filament\\Actions\\Action', 'iconButton'],
            'Filament\\Tables\\Filters\\MultiSelectFilter' => ['Filament\\Tables\\Filters\\SelectFilter', 'multiple'],
            default => null,
        };

        if (! $replacement) {
            return null;
        }

        $node->class = new FullyQualified($replacement[0]);

        return new MethodCall($node, $replacement[1]);
    }
}
