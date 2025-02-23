<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Component;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Type\ObjectType;
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
                'class' => [
                    Page::class,
                ],
                'changes' => [
                    'getFooterWidgetsColumns' => function (ClassMethod $node): void {
                        $node->returnType = new UnionType([new Identifier('int'), new Identifier('array')]);
                    },
                    'getHeaderWidgetsColumns' => function (ClassMethod $node): void {
                        $node->returnType = new UnionType([new Identifier('int'), new Identifier('array')]);
                    },
                    'getSubNavigationPosition' => function (ClassMethod $node): void {
                        $node->flags &= Modifiers::STATIC;
                    },
                ],
            ],
            [
                'class' => [
                    CreateRecord::class,
                ],
                'changes' => [
                    'canCreateAnother' => function (ClassMethod $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                ],
            ],
            [
                'class' => [
                    ViewRecord::class,
                ],
                'changes' => [
                    'infolist' => function (ClassMethod $node): void {
                        $param = new Param(new Variable('schema'));
                        $param->type = new Name('\\Filament\\Schemas\\Schema');

                        $node->params = [$param];
                    },
                ],
            ],
            [
                'class' => [
                    Dashboard::class,
                ],
                'changes' => [
                    'getColumns' => function (ClassMethod $node): void {
                        $node->returnType = new UnionType([new Identifier('int'), new Identifier('array')]);
                    },
                ],
            ],
            [
                'class' => [
                    Component::class,
                ],
                'changes' => [
                    'getChildComponents' => function (ClassMethod $node): void {
                        $node->name = new Identifier('getDefaultChildComponents');
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

        $classes = array_map(fn (string $class): string => ltrim($class, '\\'), $classes);

        foreach ($classes as $classToCheck) {
            if ($this->isObjectType($class, new ObjectType($classToCheck))) {
                return true;
            }
        }

        return false;
    }
}
