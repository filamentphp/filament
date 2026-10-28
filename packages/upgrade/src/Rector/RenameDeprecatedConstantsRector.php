<?php

namespace Filament\Upgrade\Rector;

use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Tables\View\TablesIconAlias;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
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
            'filament\\tables\\view\\tablesiconalias' => [
                'FILTERS_QUERY_BUILDER_CONSTRAINTS_BOOLEAN' => 'CONSTRAINTS_BOOLEAN',
                'FILTERS_QUERY_BUILDER_CONSTRAINTS_DATE' => 'CONSTRAINTS_DATE',
                'FILTERS_QUERY_BUILDER_CONSTRAINTS_NUMBER' => 'CONSTRAINTS_NUMBER',
                'FILTERS_QUERY_BUILDER_CONSTRAINTS_RELATIONSHIP' => 'CONSTRAINTS_RELATIONSHIP',
                'FILTERS_QUERY_BUILDER_CONSTRAINTS_SELECT' => 'CONSTRAINTS_SELECT',
                'FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT' => 'CONSTRAINTS_TEXT',
            ],
            'filament\\view\\panelsiconalias' => [
                'PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN' => 'AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN',
                'PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN_RTL' => 'AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN_RTL',
            ],
            default => [],
        };

        $replacement = $renames[$node->name->name] ?? null;

        if (! $replacement) {
            return null;
        }

        if ($this->isName($node->class, TablesIconAlias::class)) {
            $node->class = new FullyQualified(QueryBuilderIconAlias::class);
        }

        $node->name = new Identifier($replacement);

        return $node;
    }
}
