<?php

namespace Filament\QueryBuilder\Models\Scopes;

use Closure;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Forms\Components\RuleBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class QueryBuilderScope
{
    /**
     * @param  array<string, mixed>  $rules
     * @param  array<Constraint>  $constraints
     */
    public function __construct(protected array $rules, protected array $constraints)
    {
        $this->constraints = Arr::mapWithKeys(
            $this->constraints,
            fn (Constraint $constraint) => [$constraint->getName() => $constraint],
        );
    }

    /**
     * @param  array<string, mixed>  $rules
     * @param  array<Constraint>  $constraints
     */
    public static function make(array $rules, array $constraints): static
    {
        return app(static::class, ['rules' => $rules, 'constraints' => $constraints]);
    }

    public function __invoke(Builder $query): Builder
    {
        $this->applyToBaseQuery($query);

        $query->where(fn (Builder $query) => $this->applyToQuery($query));

        return $query;
    }

    /**
     * @param  array<string, mixed> | null  $rules
     */
    public function applyToBaseQuery(Builder $query, ?array $rules = null): Builder
    {
        $rules ??= $this->rules;

        foreach ($rules as $rule) {
            if ($rule['type'] === RuleBuilder::OR_BLOCK_NAME) {
                foreach ($rule['data'][RuleBuilder::OR_BLOCK_GROUPS_REPEATER_NAME] as $orGroup) {
                    $this->applyToBaseQuery(
                        $query,
                        $orGroup['rules'],
                    );
                }

                continue;
            }

            $this->tapOperatorFromRule(
                $rule,
                fn (Operator $operator) => $operator->applyToBaseFilterQuery($query),
            );
        }

        return $query;
    }

    /**
     * @param  array<string, mixed> | null  $rules
     */
    public function applyToQuery(Builder $query, ?array $rules = null): Builder
    {
        $rules ??= $this->rules;

        foreach ($rules as $rule) {
            if ($rule['type'] === RuleBuilder::OR_BLOCK_NAME) {
                $query->where(function (Builder $query) use ($rule): void {
                    $isFirst = true;

                    foreach ($rule['data'][RuleBuilder::OR_BLOCK_GROUPS_REPEATER_NAME] as $orGroup) {
                        $query->{$isFirst ? 'where' : 'orWhere'}(function (Builder $query) use ($orGroup): void {
                            $this->applyToQuery(
                                $query,
                                $orGroup['rules'],
                            );
                        });

                        $isFirst = false;
                    }
                });

                continue;
            }

            $this->tapOperatorFromRule(
                $rule,
                fn (Operator $operator) => $operator->applyToBaseQuery($query),
            );
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    protected function tapOperatorFromRule(array $rule, Closure $callback): void
    {
        $constraint = $this->constraints[$rule['type']] ?? null;

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

        $constraint
            ->settings($rule['data']['settings'] ?? [])
            ->inverse($isInverseOperator);

        $operator
            ->constraint($constraint)
            ->settings($rule['data']['settings'] ?? [])
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
