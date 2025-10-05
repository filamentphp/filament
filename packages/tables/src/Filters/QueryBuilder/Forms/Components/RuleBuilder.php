<?php

namespace Filament\Tables\Filters\QueryBuilder\Forms\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Illuminate\Support\Str;

class RuleBuilder extends Builder
{
    use HasConstraints;

    public const OR_BLOCK_NAME = 'or';

    public const OR_BLOCK_GROUPS_REPEATER_NAME = 'groups';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('filament-tables::filters/query-builder.form.rules.label'))
            ->blocks(function (): array {
                return [
                    ...array_map(
                        fn (Constraint $constraint): Builder\Block => $constraint->getBuilderBlock(),
                        $this->getConstraints(),
                    ),
                    Builder\Block::make(static::OR_BLOCK_NAME)
                        ->label(__('filament-tables::filters/query-builder.form.or_groups.block.label'))
                        ->icon(Heroicon::Slash)
                        ->schema([
                            Flex::make(function (Flex $component): array {
                                /** @var Builder $builder */
                                $builder = $component->getContainer()->getParentComponent()->getContainer()->getParentComponent();

                                return [
                                    Repeater::make(static::OR_BLOCK_GROUPS_REPEATER_NAME)
                                        ->label(__('filament-tables::filters/query-builder.form.or_groups.label'))
                                        ->schema([
                                            Flex::make([
                                                static::make('rules')
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
                                            ->label(__('filament-tables::filters/query-builder.actions.add_rule_group.label'))
                                            ->icon(Heroicon::Plus)
                                            ->hidden(fn (): bool => filled(array_filter($component->getRawState(), fn (array $itemState): bool => blank($itemState['rules'])))))
                                        ->addActionAlignment(Alignment::End)
                                        ->labelBetweenItems(__('filament-tables::filters/query-builder.item_separators.or'))
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
                ->label(__('filament-tables::filters/query-builder.actions.add_rule.label'))
                ->icon(Heroicon::Plus))
            ->addBetweenAction(fn (Action $action) => $action->hidden())
            ->addActionAlignment(Alignment::Start)
            ->hiddenLabel()
            ->labelBetweenItems(__('filament-tables::filters/query-builder.item_separators.and'))
            ->blockHeaders(false)
            ->cloneable()
            ->generateUuidUsing(fn (): string => Str::random(4))
            ->live(onBlur: true)
            ->partiallyRenderAfterActionsCalled(false)
            ->extraAttributes(['class' => 'fi-fo-builder-not-contained']);
    }
}
