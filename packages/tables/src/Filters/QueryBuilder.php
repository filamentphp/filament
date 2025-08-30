<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use Filament\Tables\Filters\QueryBuilder\Forms\Components\RuleBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use LogicException;

class QueryBuilder extends BaseFilter
{
    use HasConstraints;

    /**
     * @var array<string, ?int> | null
     */
    protected ?array $constraintPickerColumns = [];

    protected string | Closure | null $constraintPickerWidth = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-tables::filters/query-builder.label'));

        $this->schema(fn (QueryBuilder $filter): array => [
            RuleBuilder::make('rules')
                ->label($filter->getLabel())
                ->constraints($filter->getConstraints())
                ->blockPickerColumns($filter->getConstraintPickerColumns())
                ->blockPickerWidth($filter->getConstraintPickerWidth())
                ->live(onBlur: true),
        ]);

        $this->query(function (Builder $query, array $data): void {
            $this->applyRulesToQuery($query, $data['rules'], $this->getRuleBuilder());
        });

        $this->baseQuery(function (Builder $query, array $data): void {
            $this->applyRulesToBaseQuery($query, $data['rules'], $this->getRuleBuilder());
        });

        $this->columnSpanFull();
    }

    public static function getDefaultName(): ?string
    {
        return 'queryBuilder';
    }

    public function getActiveCount(): int
    {
        return $this->countRules($this->getState()['rules'], $this->getRuleBuilder());
    }

    /**
     * @param  array<string, mixed>  $rules
     */
    protected function countRules(array $rules, RuleBuilder $ruleBuilder): int
    {
        $count = 0;

        foreach ($rules as $ruleIndex => $rule) {
            $ruleBuilderBlockContainer = $ruleBuilder->getChildSchema($ruleIndex);

            if ($rule['type'] === RuleBuilder::OR_BLOCK_NAME) {
                foreach ($rule['data'][RuleBuilder::OR_BLOCK_GROUPS_REPEATER_NAME] as $orGroupIndex => $orGroup) {
                    $count += $this->countRules(
                        $orGroup['rules'],
                        $this->getNestedRuleBuilder($ruleBuilderBlockContainer, $orGroupIndex),
                    );
                }

                continue;
            }

            try {
                $ruleBuilderBlockContainer->validate();
            } catch (ValidationException) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $rules
     * @return Builder<TModel>
     */
    public function applyRulesToQuery(Builder $query, array $rules, RuleBuilder $ruleBuilder): Builder
    {
        foreach ($rules as $ruleIndex => $rule) {
            $ruleBuilderBlockContainer = $ruleBuilder->getChildSchema($ruleIndex);

            if ($rule['type'] === RuleBuilder::OR_BLOCK_NAME) {
                $query->where(function (Builder $query) use ($rule, $ruleBuilderBlockContainer): void {
                    $isFirst = true;

                    foreach ($rule['data'][RuleBuilder::OR_BLOCK_GROUPS_REPEATER_NAME] as $orGroupIndex => $orGroup) {
                        $query->{$isFirst ? 'where' : 'orWhere'}(function (Builder $query) use ($orGroup, $orGroupIndex, $ruleBuilderBlockContainer): void {
                            $this->applyRulesToQuery(
                                $query,
                                $orGroup['rules'],
                                $this->getNestedRuleBuilder($ruleBuilderBlockContainer, $orGroupIndex),
                            );
                        });

                        $isFirst = false;
                    }
                });

                continue;
            }

            $this->tapOperatorFromRule(
                $rule,
                $ruleBuilderBlockContainer,
                fn ($operator) => $operator->applyToBaseQuery($query),
            );
        }

        return $query;
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $rules
     * @return Builder<TModel>
     */
    public function applyRulesToBaseQuery(Builder $query, array $rules, RuleBuilder $ruleBuilder): Builder
    {
        foreach ($rules as $ruleIndex => $rule) {
            $ruleBuilderBlockContainer = $ruleBuilder->getChildSchema($ruleIndex);

            if ($rule['type'] === RuleBuilder::OR_BLOCK_NAME) {
                foreach ($rule['data'][RuleBuilder::OR_BLOCK_GROUPS_REPEATER_NAME] as $orGroupIndex => $orGroup) {
                    $this->applyRulesToBaseQuery(
                        $query,
                        $orGroup['rules'],
                        $this->getNestedRuleBuilder($ruleBuilderBlockContainer, $orGroupIndex),
                    );
                }

                continue;
            }

            $this->tapOperatorFromRule(
                $rule,
                $ruleBuilderBlockContainer,
                fn ($operator) => $operator->applyToBaseFilterQuery($query),
            );
        }

        return $query;
    }

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public function constraintPickerColumns(array | int | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->constraintPickerColumns = [
            ...($this->constraintPickerColumns ?? []),
            ...$columns,
        ];

        return $this;
    }

    /**
     * @return array<string, ?int> | int | null
     */
    public function getConstraintPickerColumns(?string $breakpoint = null): array | int | null
    {
        $columns = $this->constraintPickerColumns ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

    public function constraintPickerWidth(string | Closure | null $width): static
    {
        $this->constraintPickerWidth = $width;

        return $this;
    }

    public function getConstraintPickerWidth(): ?string
    {
        return $this->evaluate($this->constraintPickerWidth);
    }

    protected function getRuleBuilder(): RuleBuilder
    {
        $builder = $this->getSchema()->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

        if (! ($builder instanceof RuleBuilder)) {
            throw new LogicException('No rule builder component found.');
        }

        return $builder;
    }

    protected function getNestedRuleBuilder(Schema $schema, string $orGroupIndex): RuleBuilder
    {
        $builder = $schema
            ->getComponent(fn (Component $component): bool => $component instanceof Repeater)
            ->getChildSchema($orGroupIndex)
            ->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

        if (! ($builder instanceof RuleBuilder)) {
            throw new LogicException('No nested rule builder component found.');
        }

        return $builder;
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    protected function tapOperatorFromRule(array $rule, Schema $schema, Closure $callback): void
    {
        $constraint = $this->getConstraint($rule['type']);

        if (! $constraint) {
            return;
        }

        $operator = $rule['data'][$constraint::OPERATOR_SELECT_NAME];

        if (blank($operator)) {
            return;
        }

        [$operatorName, $isInverseOperator] = $constraint->parseOperatorString($operator);

        $operator = $constraint->getOperator($operatorName);

        if (! $operator) {
            return;
        }

        try {
            $schema->validate();
        } catch (ValidationException) {
            return;
        }

        $constraint
            ->settings($rule['data']['settings'])
            ->inverse($isInverseOperator);

        $operator
            ->constraint($constraint)
            ->settings($rule['data']['settings'])
            ->inverse($isInverseOperator);

        $callback($operator);

        $constraint
            ->settings(null)
            ->inverse(null);

        $operator
            ->constraint(null)
            ->settings(null)
            ->inverse(null);
    }
}
