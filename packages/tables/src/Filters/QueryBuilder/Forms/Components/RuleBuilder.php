<?php

namespace Filament\Tables\Filters\QueryBuilder\Forms\Components;

use Illuminate\Support\Str;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;

class RuleBuilder extends Builder
{
    use HasConstraints;

    public const OR_BLOCK_NAME = 'or';

    public const OR_BLOCK_GROUPS_REPEATER_NAME = 'groups';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->blocks(function (Builder $component): array {
                return [
                    ...array_map(
                        fn (Constraint $constraint): Builder\Block => $constraint->getBuilderBlock(),
                        $this->getConstraints(),
                    ),
                    Builder\Block::make(static::OR_BLOCK_NAME)
                        ->label(function (?array $state, ?string $uuid) use ($component) {
                            if (blank($state) || blank($uuid)) {
                                return 'Disjunction (OR)';
                            }

                            if (! count($state[static::OR_BLOCK_GROUPS_REPEATER_NAME] ?? [])) {
                                return '(No rules)';
                            }

                            $repeater = $component->getChildComponentContainer($uuid)
                                ->getComponent(fn (Component $component): bool => $component instanceof Repeater);

                            $itemLabels = collect($repeater->getChildComponentContainers())
                                ->map(fn (ComponentContainer $blockContainer, string $blockContainerUuid): string => $repeater->getItemLabel($blockContainerUuid));

                            return (($state['not'] ?? false) ? 'NOT ' : '') . '(' . $itemLabels->implode(') OR (') . ')';
                        })
                        ->icon('heroicon-m-bars-4')
                        ->schema(fn (): array => [
                            Repeater::make(static::OR_BLOCK_GROUPS_REPEATER_NAME)
                                ->schema(fn (): array => [
                                    static::make('rules')
                                        ->constraints($this->getConstraints())
                                        ->blockPickerColumns($this->getBlockPickerColumns())
                                        ->blockPickerWidth($this->getBlockPickerWidth()),
                                ])
                                ->addAction(fn (Action $action) => $action
                                    ->label('Add rule group')
                                    ->icon('heroicon-s-plus'))
                                ->labelBetweenItems('OR')
                                ->collapsible()
                                ->expandAllAction(fn (Action $action) => $action->hidden())
                                ->collapseAllAction(fn (Action $action) => $action->hidden())
                                ->itemLabel(function (ComponentContainer $container, array $state): string {
                                    $builder = $container->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

                                    $blockLabels = collect($builder->getChildComponentContainers())
                                        ->map(fn (ComponentContainer $blockContainer, string $blockUuid): string => $blockContainer->getParentComponent()->getLabel($blockContainer->getRawState(), $blockUuid));

                                    if ($blockLabels->isEmpty()) {
                                        return '(No rules)';
                                    }

                                    return (($state['not'] ?? false) ? 'NOT ' : '') . '(' . $blockLabels->implode(') AND (') . ')';
                                })
                                ->truncateItemLabel(false)
                                ->cloneable()
                                ->reorderable(false)
                                ->hiddenLabel()
                                ->generateUuidUsing(fn (): string => Str::random(4)),
                            Checkbox::make('not')
                                ->label('NOT'),
                        ]),
                ];
            })
            ->addAction(fn (Action $action) => $action
                ->label('Add rule')
                ->icon('heroicon-s-plus'))
            ->addBetweenAction(fn (Action $action) => $action->hidden())
            ->labelBetweenItems('AND')
            ->hiddenLabel()
            ->blockNumbers(false)
            ->collapsible()
            ->cloneable()
            ->reorderable(false)
            ->expandAllAction(fn (Action $action) => $action->hidden())
            ->collapseAllAction(fn (Action $action) => $action->hidden())
            ->truncateBlockLabel(false)
            ->generateUuidUsing(fn (): string => Str::random(4));
    }
}
