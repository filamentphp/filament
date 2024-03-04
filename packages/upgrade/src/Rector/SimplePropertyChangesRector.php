<?php

namespace Filament\Upgrade\Rector;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\VarLikeIdentifier;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class SimplePropertyChangesRector extends AbstractRector
{
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
                    'middlewares' => function (Property $node) {
                        $node->props[0]->name = new VarLikeIdentifier('routeMiddleware');
                    },
                ],
            ],
            [
                'class' => [
                    'Filament\\Resources\\Resource',
                    'Filament\\Resources\\RelationManagers\\RelationManager',
                ],
                'classIdentifier' => 'extends',
                'changes' => [
                    'shouldIgnorePolicies' => function (Property $node) {
                        $node->props[0]->name = new VarLikeIdentifier('shouldSkipAuthorization');
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

            foreach ($change['changes'] as $propertyName => $modifier) {
                foreach ($node->getProperties() as $property) {
                    if (! $this->isName($property, $propertyName)) {
                        continue;
                    }

                    $modifier($property);

                    $touched = true;
                }
            }
        }

        return $touched ? $node : null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fix property definitions',
            [
                new CodeSample(
                    'protected static string | array $middlewares = [];',
                    'protected static string | array $routeMiddleware = [];',
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
