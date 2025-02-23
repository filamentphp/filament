<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Closure;
use Exception;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasIcon;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Validation\ValidationException;

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

    protected QueryBuilder $filter;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $constraintClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Constraint of class [$constraintClass] must have a unique name, passed to the [make()] method.");
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
            ->label(function (Block $component, ?array $state, ?string $uuid) {
                if (blank($state[static::OPERATOR_SELECT_NAME] ?? null)) {
                    return $this->getLabel();
                }

                [$operatorName, $isInverseOperator] = $this->parseOperatorString($state[static::OPERATOR_SELECT_NAME]);

                $operator = $this->getOperator($operatorName);

                if (! $operator) {
                    return $this->getLabel();
                }

                try {
                    $component->getContainer()->getParentComponent()
                        ->getChildSchema($uuid)
                        ->getComponent('settings')
                        ->getChildSchema()
                        ->validate();
                } catch (ValidationException $exception) {
                    return $this->getLabel();
                }

                $this
                    ->settings($state['settings'])
                    ->inverse($isInverseOperator);

                $operator
                    ->constraint($this)
                    ->settings($state['settings'])
                    ->inverse($isInverseOperator);

                try {
                    return $operator->getSummary();
                } finally {
                    $this
                        ->settings(null)
                        ->inverse(null);

                    $operator
                        ->constraint(null)
                        ->settings(null)
                        ->inverse(null);
                }
            })
            ->icon($this->getIcon())
            ->schema(function (): array {
                $operatorSelectOptions = $this->getOperatorSelectOptions();

                return [
                    Select::make(static::OPERATOR_SELECT_NAME)
                        ->label(__('filament-tables::filters/query-builder.form.operator.label'))
                        ->options($operatorSelectOptions)
                        ->default(array_key_first($operatorSelectOptions))
                        ->live()
                        ->afterStateUpdated(fn (Select $component, Get $get) => $component
                            ->getContainer()
                            ->getComponent('settings')
                            ->getChildSchema()
                            ->fill($get('settings'))),
                    Group::make(function ($component, Get $get): array {
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
                        ->statePath('settings')
                        ->key('settings')
                        ->columnSpan(2)
                        ->columns(2),
                ];
            })
            ->columns(3);
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

    public function relationship(string $name, string $titleAttribute, ?Closure $modifyQueryUsing = null): static
    {
        $this->attribute("{$name}.{$titleAttribute}");

        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;

        return $this;
    }

    public function filter(QueryBuilder $filter): static
    {
        $this->filter = $filter;

        return $this;
    }

    public function getFilter(): QueryBuilder
    {
        return $this->filter;
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
