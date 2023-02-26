<?php

namespace Filament\Upgrade\Rectors;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceParentClassRector extends AbstractRector
{
    /**
     * @return array<string, string>
     */
    public function getChanges(): array
    {
        return [
            'Filament\\PluginServiceProvider' => 'Spatie\\LaravelPackageTools\\PackageServiceProvider',
            'Filament\\Resources\\RelationManagers\\BelongsToManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\HasManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\HasManyThroughRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\MorphManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\MorphToManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
        ];
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        /** @var Class_ $node */
        $changes = $this->getChanges();

        foreach ($changes as $parentClassToRemove => $parentClassToAdd) {
            if (! $this->isName($node->extends, $parentClassToRemove)) {
                continue;
            }

            $node->extends = new Name("\\{$parentClassToAdd}");

            return $node;
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace the parent class with another',
            [
                new CodeSample(
                    'MembersRelationManager extends BelongsToManyRelationManager',
                    'MembersRelationManager extends RelationManager',
                ),
            ]
        );
    }
}
