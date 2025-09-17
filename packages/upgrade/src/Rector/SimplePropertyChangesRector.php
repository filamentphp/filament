<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Auth\Pages\EditProfile;
use Filament\Pages\BasePage;
use Filament\Pages\Page;
use Filament\Pages\SimplePage;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Widget;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;

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
                    BasePage::class,
                    RelationManager::class,
                    Widget::class,
                ],
                'changes' => [
                    'view' => function (Property $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                ],
            ],
            [
                'class' => [
                    SimplePage::class,
                    EditProfile::class,
                ],
                'changes' => [
                    'maxWidth' => function (Property $node): void {
                        $node->type = new UnionType([new FullyQualified('Filament\\Support\\Enums\\Width'), new Name('string'), new Name('null')]);
                    },
                ],
            ],
            [
                'class' => [
                    BasePage::class,
                ],
                'changes' => [
                    'maxContentWidth' => function (Property $node): void {
                        $node->type = new UnionType([new FullyQualified('Filament\\Support\\Enums\\Width'), new Name('string'), new Name('null')]);
                    },
                ],
            ],
            [
                'class' => [
                    Resource::class,
                    Page::class,
                ],
                'changes' => [
                    'activeNavigationIcon' => function (Property $node): void {
                        $node->type = new Name('string | \BackedEnum | null');
                    },
                    'navigationIcon' => function (Property $node): void {
                        $node->type = new Name('string | \BackedEnum | null');
                    },
                    'navigationGroup' => function (Property $node): void {
                        $node->type = new Name('string | \UnitEnum | null');
                    },
                    'subNavigationPosition' => function (Property $node): void {
                        $node->type = new Name('?\Filament\Pages\Enums\SubNavigationPosition');
                    },
                ],
            ],
            [
                'class' => [
                    RelationManager::class,
                ],
                'changes' => [
                    'icon' => function (Property $node): void {
                        $node->type = new Name('string | \BackedEnum | null');
                    },
                ],
            ],
            [
                'class' => [
                    ChartWidget::class,
                    StatsOverviewWidget::class,
                ],
                'changes' => [
                    'pollingInterval' => function (Property $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                ],
            ],
            [
                'class' => [
                    ChartWidget::class,
                ],
                'changes' => [
                    'color' => function (Property $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                    'heading' => function (Property $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                    'description' => function (Property $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                    'maxHeight' => function (Property $node): void {
                        $node->flags &= ~Modifiers::STATIC;
                    },
                    'options' => function (Property $node): void {
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
