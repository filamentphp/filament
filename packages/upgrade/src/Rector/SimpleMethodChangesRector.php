<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Resources\Pages\CreateRecord;
use PhpParser\Modifiers;
use PhpParser\Node;
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
                'changes' => [
                    'getSubNavigationPosition' => function (ClassMethod $node) {
                        $node->flags &= Modifiers::STATIC;
                    },
                ],
            ],
            [
                'class' => [
                    CreateRecord::class,
                ],
                'classIdentifier' => 'extends',
                'changes' => [
                    'canCreateAnother' => function (ClassMethod $node) {
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
        if (! array_key_exists('class', $change)) {
            return true;
        }

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
