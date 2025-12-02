<?php

namespace Filament\QueryBuilder\Forms\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\View\FormsIconAlias;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class RuleBuilder extends Builder
{
    public const OR_BLOCK_NAME = 'or';

    public const OR_BLOCK_GROUPS_REPEATER_NAME = 'groups';

    /** @var array<Constraint> */
    protected array $constraints = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('filament-query-builder::query-builder.form.rules.label'))
            ->blocks(function (): array {
                return [
                    ...array_map(
                        fn (Constraint $constraint): Builder\Block => $constraint->getBuilderBlock(),
                        $this->getConstraints(),
                    ),
                    Builder\Block::make(static::OR_BLOCK_NAME)
                        ->label(__('filament-query-builder::query-builder.form.or_groups.block.label'))
                        ->icon(FilamentIcon::resolve(QueryBuilderIconAlias::OR_GROUP_BLOCK) ?? Heroicon::Slash)
                        ->schema([
                            Flex::make(function (Flex $component): array {
                                /** @var Builder $builder */
                                $builder = $component->getContainer()->getParentComponent()->getContainer()->getParentComponent();

                                return [
                                    Repeater::make(static::OR_BLOCK_GROUPS_REPEATER_NAME)
                                        ->label(__('filament-query-builder::query-builder.form.or_groups.label'))
                                        ->schema([
                                            Flex::make([
                                                static::make('rules')
                                                    ->hiddenLabel()
                                                    ->partiallyRenderAfterActionsCalled($builder->shouldPartiallyRenderAfterActionsCalled())
                                                    ->constraints($this->getConstraints())
                                                    ->blockPickerColumns($this->getBlockPickerColumns())
                                                    ->blockPickerWidth($this->getBlockPickerWidth()),
                                                Actions::make(function (Actions $component): array {
                                                    /** @var Repeater $repeater */
                                                    $repeater = $component->getContainer()->getParentComponent()->getContainer()->getParentComponent();

                                                    return [
                                                        Action::make($deleteActionName = $repeater->getDeleteActionName())
                                                            ->label(__('filament-forms::components.repeater.actions.delete.label'))
                                                            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_DELETE) ?? Heroicon::Trash)
                                                            ->color('danger')
                                                            ->iconButton()
                                                            ->size(Size::Small)
                                                            ->action($repeater->getAction($deleteActionName)->arguments(['item' => (string) str($component->getContainer()->getParentComponent()->getContainer()->getStatePath(isAbsolute: false))->beforeLast('.data')])->getLivewireClickHandler())
                                                            ->visible(fn (Get $get): bool => blank($get('rules')) && (count($repeater->getRawState()) > 2)),
                                                    ];
                                                })->grow(false),
                                            ])->verticallyAlignCenter(),
                                        ])
                                        ->addAction(fn (Action $action, Repeater $component) => $action
                                            ->label(__('filament-query-builder::query-builder.actions.add_rule_group.label'))
                                            ->icon(FilamentIcon::resolve(QueryBuilderIconAlias::OR_GROUP_ADD_GROUP_ACTION) ?? Heroicon::Plus)
                                            ->hidden(fn (): bool => filled(array_filter($component->getRawState(), fn (array $itemState): bool => blank($itemState['rules'])))))
                                        ->addActionAlignment(Alignment::End)
                                        ->labelBetweenItems(__('filament-query-builder::query-builder.item_separators.or'))
                                        ->itemHeaders(false)
                                        ->defaultItems(2)
                                        ->minItems(2)
                                        ->cloneable()
                                        ->hiddenLabel()
                                        ->generateUuidUsing(fn (): string => Str::random(4))
                                        ->partiallyRenderAfterActionsCalled($builder->shouldPartiallyRenderAfterActionsCalled()),
                                    Actions::make([
                                        Action::make($cloneActionName = $builder->getCloneActionName())
                                            ->label(__('filament-forms::components.builder.actions.clone.label'))
                                            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_CLONE) ?? Heroicon::Square2Stack)
                                            ->color('gray')
                                            ->iconButton()
                                            ->size(Size::Small)
                                            ->action($builder->getAction($cloneActionName)->arguments(['item' => (string) str($component->getContainer()->getStatePath(isAbsolute: false))->beforeLast('.data')])->getLivewireClickHandler()),
                                        Action::make($deleteActionName = $builder->getDeleteActionName())
                                            ->label(__('filament-forms::components.builder.actions.delete.label'))
                                            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_DELETE) ?? Heroicon::Trash)
                                            ->color('danger')
                                            ->iconButton()
                                            ->size(Size::Small)
                                            ->action($builder->getAction($deleteActionName)->arguments(['item' => (string) str($component->getContainer()->getStatePath(isAbsolute: false))->beforeLast('.data')])->getLivewireClickHandler()),
                                    ])->grow(false),
                                ];
                            }),
                        ]),
                ];
            })
            ->addAction(fn (Action $action) => $action
                ->label(__('filament-query-builder::query-builder.actions.add_rule.label'))
                ->icon(FilamentIcon::resolve(QueryBuilderIconAlias::ADD_RULE_ACTION) ?? Heroicon::Plus))
            ->addBetweenAction(fn (Action $action) => $action->hidden())
            ->addActionAlignment(Alignment::Start)
            ->labelBetweenItems(__('filament-query-builder::query-builder.item_separators.and'))
            ->blockHeaders(false)
            ->cloneable()
            ->generateUuidUsing(fn (): string => Str::random(4))
            ->partiallyRenderAfterActionsCalled(false)
            ->extraAttributes(['class' => 'fi-fo-builder-not-contained']);
    }

    /**
     * @param  array<Constraint>  $constraints
     */
    public function constraints(array $constraints): static
    {
        foreach ($constraints as $constraint) {
            $this->constraints[$constraint->getName()] = $constraint;
        }

        return $this;
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->evaluate($this->constraints);
    }

    public function getConstraint(string $name): ?Constraint
    {
        return $this->getConstraints()[$name] ?? null;
    }

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public function constraintPickerColumns(array | int | null $columns = 2): static
    {
        $this->blockPickerColumns($columns);

        return $this;
    }
}
