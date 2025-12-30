<?php

namespace Filament\QueryBuilder\Constraints;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class Constraint extends Component
{
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasOperators;
    use HasIcon;

    public const OPERATOR_SELECT_NAME = 'operator';

    protected string $evaluationIdentifier = 'constraint';

    protected string | Closure | null $attribute = null;

    protected string | Closure | null $attributeLabel = null;

    protected string | Closure | null $relationship = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $settings = null;

    protected ?bool $isInverse = null;

    /**
     * @var ?class-string<Model>
     */
    protected ?string $model = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $constraintClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new LogicException("Constraint of class [$constraintClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($constraintClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getBuilderBlock(): Block
    {
        return Block::make($this->getName())
            ->label($this->getLabel())
            ->icon($this->getIcon())
            ->schema(function (): array {
                $operatorSelectOptions = $this->getOperatorSelectOptions();

                return [
                    Flex::make(function (Flex $component) use ($operatorSelectOptions): array {
                        /** @var Builder $builder */
                        $builder = $component->getContainer()->getParentComponent()->getContainer()->getParentComponent();

                        return [
                            Grid::make(['@md' => 2, '@xl' => 3, '!@sm' => 2, '!@md' => 3])
                                ->schema([
                                    Select::make(static::OPERATOR_SELECT_NAME)
                                        ->label($this->getLabel())
                                        ->options($operatorSelectOptions)
                                        ->default(array_key_first($operatorSelectOptions))
                                        ->live()
                                        ->afterStateUpdated(fn (Select $component, Get $get) => $component
                                            ->getContainer()
                                            ->getComponent('settings', withHidden: true)
                                            ->getChildSchema()
                                            ->fill($get('settings'))),
                                    Group::make(function (Get $get): array {
                                        $operator = $get(static::OPERATOR_SELECT_NAME);

                                        if (blank($operator)) {
                                            return [];
                                        }

                                        [$operatorName] = $this->parseOperatorString($operator);

                                        $operator = $this->getOperator($operatorName);

                                        if (! $operator) {
                                            return [];
                                        }

                                        $operator->constraint($this);

                                        try {
                                            return $operator->getFormSchema();
                                        } finally {
                                            $operator->constraint(null);
                                        }
                                    })
                                        ->dense()
                                        ->statePath('settings')
                                        ->key('settings')
                                        ->columnSpan(['@xl' => 2, '!@md' => 2])
                                        ->columns(['@xl' => 2, '!@md' => 2])
                                        ->visible(fn (Group $component): bool => filled($component->getChildSchema()->getComponents())),
                                ])
                                ->dense(),
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
                    })->gridContainer(),
                ];
            });
    }

    /**
     * @return array<string, string>
     */
    public function getOperatorSelectOptions(): array
    {
        $options = [];

        foreach ($this->getOperators() as $operatorName => $operator) {
            $operator->constraint($this);

            $options[$operatorName] = $operator->inverse(false)->getLabel();
            $options["{$operatorName}.inverse"] = $operator->inverse()->getLabel();

            $operator->constraint(null);

            $operator->inverse(null);
        }

        return $options;
    }

    /**
     * @return array{string, bool}
     */
    public function parseOperatorString(string $operator): array
    {
        if (str($operator)->endsWith('.inverse')) {
            return [(string) str($operator)->beforeLast('.'), true];
        }

        return [$operator, false];
    }

    public function attribute(string | Closure | null $name): static
    {
        $this->attribute = $name;

        return $this;
    }

    public function attributeLabel(string | Closure | null $label): static
    {
        $this->attributeLabel = $label;

        return $this;
    }

    public function modifyRelationshipQueryUsing(?Closure $modifyQueryUsing): static
    {
        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;

        return $this;
    }

    public function relationship(string $name, string $titleAttribute, ?Closure $modifyQueryUsing = null): static
    {
        $this->attribute("{$name}.{$titleAttribute}");

        $this->modifyRelationshipQueryUsing($modifyQueryUsing);

        return $this;
    }

    /**
     * @param  ?class-string<Model>  $model
     */
    public function model(?string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return ?class-string<Model>
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    public function getAttributeLabel(): string
    {
        return $this->evaluate($this->attributeLabel) ?? $this->getLabel();
    }

    public function queriesRelationships(): bool
    {
        return str($this->getAttribute())->contains('.');
    }

    public function getRelationshipName(): string
    {
        return (string) str($this->getAttribute())->beforeLast('.');
    }

    public function getAttributeForQuery(): string
    {
        return (string) str($this->getAttribute())->afterLast('.');
    }

    public function getModifyRelationshipQueryUsing(): ?Closure
    {
        return $this->modifyRelationshipQueryUsing;
    }

    /**
     * @param  array<string, mixed> | null  $settings
     */
    public function settings(?array $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

    public function inverse(?bool $condition = true): static
    {
        $this->isInverse = $condition;

        return $this;
    }

    /**
     * @return array<string, mixed> | null
     */
    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function isInverse(): ?bool
    {
        return $this->isInverse;
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'isInverse' => [$this->isInverse()],
            'settings' => [$this->getSettings()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
