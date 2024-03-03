<?php

namespace Filament\Upgrade\Rector;

use Closure;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Naming\VariableRenamer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class SimpleMethodChangesRector extends AbstractRector
{
    protected VariableRenamer $variableRenamer;

    public function __construct(VariableRenamer $variableRenamer)
    {
        $this->variableRenamer = $variableRenamer;
    }

    /**
     * @return array<array{
     *     class: class-string | array<class-string>,
     *     classIdentifier: string,
     *     changes: array<string, Closure>,
     * }>
     */
    public function getChanges(): array
    {
        return [
            [
                'class' => 'Filament\\Models\\Contracts\\FilamentUser',
                'classIdentifier' => 'implements',
                'changes' => [
                    'canAccessFilament' => function (ClassMethod $node) {
                        $param = new Param(new Variable('panel'));
                        $param->type = new Name('\\Filament\\Panel');

                        $node->params = [$param];
                    },
                ],
            ],
            [
                'class' => [
                    'Filament\\Pages\\Dashboard',
                    'Filament\\Pages\\Page',
                    'Filament\\Pages\\SettingsPage',
                    'Filament\\Resources\\Pages\\CreateRecord',
                    'Filament\\Resources\\Pages\\EditRecord',
                    'Filament\\Resources\\Pages\\ListRecords',
                    'Filament\\Resources\\Pages\\ManageRecords',
                    'Filament\\Resources\\Pages\\Page',
                    'Filament\\Resources\\Pages\\ViewRecord',
                ],
                'classIdentifier' => 'extends',
                'changes' => [
                    'getBreadcrumbs' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getFooterWidgetsColumns' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getHeader' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getHeaderWidgetsColumns' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getHeading' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getRouteName' => function (ClassMethod $node) {
                        $param = new Param(new Variable('panel'));
                        $param->default = new ConstFetch(new Name('null'));
                        $param->type = new Identifier('?string');

                        $node->params = [$param];
                    },
                    'getSubheading' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getTitle' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getVisibleFooterWidgets' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getVisibleHeaderWidgets' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                ],
            ],
            [
                'class' => [
                    'Filament\\Pages\\Dashboard',
                    'Filament\\Pages\\Page',
                    'Filament\\Pages\\SettingsPage',
                    'Filament\\Resources\\Pages\\CreateRecord',
                    'Filament\\Resources\\Pages\\EditRecord',
                    'Filament\\Resources\\Pages\\ListRecords',
                    'Filament\\Resources\\Pages\\ManageRecords',
                    'Filament\\Resources\\Pages\\Page',
                    'Filament\\Resources\\Pages\\ViewRecord',
                    'Filament\\Resources\\Resource',
                ],
                'classIdentifier' => 'extends',
                'changes' => [
                    'getActiveNavigationIcon' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getNavigationBadge' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getNavigationBadgeColor' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getNavigationGroup' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getNavigationIcon' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;

                        $node->returnType = new Identifier('?string');
                    },
                    'getNavigationLabel' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getNavigationSort' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getNavigationUrl' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'shouldRegisterNavigation' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                ],
            ],
            [
                'class' => 'Filament\\Pages\\Dashboard',
                'classIdentifier' => 'extends',
                'changes' => [
                    'getColumns' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getWidgets' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                ],
            ],
            [
                'class' => [
                    'Filament\\Resources\\Pages\\EditRecord',
                    'Filament\\Resources\\Pages\\ViewRecord',
                ],
                'classIdentifier' => 'extends',
                'changes' => [
                    'getFormTabLabel' => function (ClassMethod $node) {
                        $node->name = new Identifier('getContentTabLabel');
                    },
                    'mount' => function (ClassMethod $node) {
                        $node->params[0]->type = new Identifier('int | string');
                    },
                ],
            ],
            [
                'class' => 'Filament\\Resources\\Pages\\CreateRecord',
                'classIdentifier' => 'extends',
                'changes' => [
                    'canCreateAnother' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                ],
            ],
            [
                'class' => 'Filament\\Resources\\Pages\\ListRecords',
                'classIdentifier' => 'extends',
                'changes' => [
                    'table' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                ],
            ],
            [
                'class' => 'Filament\\Resources\\Resource',
                'classIdentifier' => 'extends',
                'changes' => [
                    'applyGlobalSearchAttributeConstraint' => function (ClassMethod $node) {
                        $node->params[1]->var->name = 'search';
                        $node->params[1]->type = new Identifier('string');

                        $node->params[2]->var->name = 'searchAttributes';
                        $node->params[2]->type = new Identifier('array');

                        $this->variableRenamer->renameVariableInFunctionLike(
                            $node,
                            oldName: 'searchQuery',
                            expectedName: 'search',
                        );
                    },
                    'getGlobalSearchEloquentQuery' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::PROTECTED;
                        $node->flags |= Modifiers::PUBLIC;
                    },
                    'getGlobalSearchResults' => function (ClassMethod $node) {
                        $node->params[0]->var->name = 'search';

                        $this->variableRenamer->renameVariableInFunctionLike(
                            $node,
                            oldName: 'searchQuery',
                            expectedName: 'search',
                        );
                    },
                    'getRouteBaseName' => function (ClassMethod $node) {
                        $param = new Param(new Variable('panel'));
                        $param->default = new ConstFetch(new Name('null'));
                        $param->type = new Identifier('?string');

                        $node->params = [$param];
                    },
                    'resolveRecordRouteBinding' => function (ClassMethod $node) {
                        $node->params[0]->type = new Identifier('int | string');
                    },
                ],
            ],
            [
                'class' => [
                    'Filament\\Resources\\RelationManagers\\RelationManager',
                    'Filament\\Resources\\RelationManagers\\BelongsToManyRelationManager',
                    'Filament\\Resources\\RelationManagers\\HasManyRelationManager',
                    'Filament\\Resources\\RelationManagers\\HasManyThroughRelationManager',
                    'Filament\\Resources\\RelationManagers\\MorphManyRelationManager',
                    'Filament\\Resources\\RelationManagers\\MorphToManyRelationManager',
                ],
                'classIdentifier' => 'extends',
                'changes' => [
                    'form' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                    'getInverseRelationshipName' => function (ClassMethod $node) {
                        $node->returnType = new Identifier('?string');
                    },
                    'getModelLabel' => function (ClassMethod $node) {
                        $node->returnType = new Identifier('?string');
                    },
                    'table' => function (ClassMethod $node) {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                ],
            ],
        ];
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
        $touched = false;
        foreach ($this->getChanges() as $change) {
            if (! $this->isClassMatchingChange($node, $change)) {
                continue;
            }

            foreach ($change['changes'] as $methodName => $modifier) {
                foreach ($node->getMethods() as $method) {
                    if (! $this->isName($method, $methodName)) {
                        continue;
                    }

                    $modifier($method);

                    $touched = true;
                }
            }
        }

        return $touched ? $node : null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fix method definitions',
            [
                new CodeSample(
                    'public static function form(Form $form): Form',
                    'public function form(Form $form): Form',
                ),
            ]
        );
    }

    /**
     * @param array{
     *     class: class-string | array<class-string>,
     *     classIdentifier: string,
     * } $change
     */
    public function isClassMatchingChange(Class_ $class, array $change): bool
    {
        $classes = is_array($change['class']) ?
            $change['class'] :
            [$change['class']];

        $classes = [
            ...array_map(fn (string $class): string => ltrim($class, '\\'), $classes),
            ...array_map(fn (string $class): string => '\\' . ltrim($class, '\\'), $classes),
        ];

        if ($change['classIdentifier'] === 'extends') {
            return $class->extends && $this->isNames($class->extends, $classes);
        }

        if ($change['classIdentifier'] !== 'implements') {
            return false;
        }

        return (bool) count(array_filter(
            $class->implements,
            fn (Name $interface): bool => $this->isNames($interface, $classes),
        ));
    }
}
