<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Forms\Components\RuleBuilder;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use LogicException;

class QueryBuilder extends BaseFilter
{
    /**
     * @var array<string, ?int> | null
     */
    protected ?array $constraintPickerColumns = [];

    protected string | Closure | null $constraintPickerWidth = null;

    /** @var array<Constraint> */
    protected array $constraints = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-query-builder::query-builder.label'));

        $this->schema(fn (QueryBuilder $filter): array => [
            RuleBuilder::make('rules')
                ->label($filter->getLabel())
                ->hiddenLabel()
                ->constraints($filter->getConstraints())
                ->blockPickerColumns($filter->getConstraintPickerColumns())
                ->blockPickerWidth($filter->getConstraintPickerWidth()),
        ]);

        $this->query(function (Builder $query, array $data): void {
            $this->applyRulesToQuery($query, $data['rules'], $this->getRuleBuilder());
        });

        $this->baseQuery(function (Builder $query, array $data): void {
            $this->applyRulesToBaseQuery($query, $data['rules'], $this->getRuleBuilder());
        });

        $this->indicateUsing(function (array $state): array {
            return $this->getRuleSummaries($state['rules'], $this->getRuleBuilder());
        });

        $this->columnSpanFull();
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return array<string>
     */
    public function getRuleSummaries(array $rules, RuleBuilder $ruleBuilder, int $iteration = 1): array
    {
        $summaries = [];

        foreach ($rules as $ruleIndex => $rule) {
            $ruleBuilderBlockContainer = $ruleBuilder->getChildSchema($ruleIndex);

            if ($rule['type'] === RuleBuilder::OR_BLOCK_NAME) {
                $orSummaries = [];

                foreach ($rule['data'][RuleBuilder::OR_BLOCK_GROUPS_REPEATER_NAME] as $orGroupIndex => $orGroup) {
                    $orGroupSummaries = $this->getRuleSummaries(
                        $orGroup['rules'],
                        $this->getNestedRuleBuilder($ruleBuilderBlockContainer, $orGroupIndex),
                        $iteration + 1,
                    );

                    $orSummaries[] = ((count($orGroupSummaries) > 1) ? '(' : '') . implode(' ' . __('filament-query-builder::query-builder.form.rules.item.and') . ' ', $orGroupSummaries) . ((count($orGroupSummaries) > 1) ? ')' : '');
                }

                $orSummaries = array_filter($orSummaries, filled(...));

                if (blank($orSummaries)) {
                    continue;
                }

                if (count($orSummaries) === 1) {
                    $summaries[$ruleIndex] = Arr::first($orSummaries);

                    continue;
                }

                $hasParentheses = ($iteration > 1) && (count($orSummaries) > 1);

                $summaries[$ruleIndex] = ($hasParentheses ? '(' : '') . implode(' ' . __('filament-query-builder::query-builder.form.or_groups.block.or') . ' ', $orSummaries) . ($hasParentheses ? ')' : '');

                continue;
            }

            $this->tapOperatorFromRule(
                $rule,
                $ruleBuilderBlockContainer,
                function (Operator $operator) use ($ruleIndex, &$summaries): void {
                    $summaries[$ruleIndex] = $operator->getSummary();
                },
            );
        }

        return $summaries;
    }

    public static function getDefaultName(): ?string
    {
        return 'queryBuilder';
    }

    public function getActiveCount(): int
    {
        return $this->countRules($this->getFormState()['rules'], $this->getRuleBuilder());
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
                fn (Operator $operator) => $operator->applyToBaseQuery($query),
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
                fn (Operator $operator) => $operator->applyToBaseFilterQuery($query),
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
        return array_map(fn (Constraint $constraint): Constraint => $constraint->model($this->getTable()->getModel()), $this->constraints);
    }

    public function getConstraint(string $name): ?Constraint
    {
        return $this->getConstraints()[$name] ?? null;
    }
}
