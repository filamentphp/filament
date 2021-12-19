<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait CanBeValidated
{
    protected $isRequired = false;

    protected array $rules = [];

    protected $validationAttribute = null;

    public function exists(string | callable | null $table = null, string | callable | null $column = null, ?Closure $callback = null): static
    {
        $this->rule(function (Component $component) use ($callback, $column, $table) {
            $table = $component->evaluate($table) ?? $component->getModelClass();
            $column = $component->evaluate($column) ?? $component->getName();

            $rule = Rule::exists($table, $column);

            if ($callback) {
                $rule = $this->evaluate($callback, [
                    'rule' => $rule,
                ]);
            }

            return $rule;
        }, fn (Component $component): bool => (bool) ($component->evaluate($table) ?? $component->getModelClass()));

        return $this;
    }

    public function nullable(bool | callable $condition = true): static
    {
        $this->required(function (Component $component) use ($condition): bool {
            return ! $component->evaluate($condition);
        });

        return $this;
    }

    public function required(bool | callable $condition = true): static
    {
        $this->isRequired = $condition;

        return $this;
    }

    public function rule(string | object | callable $rule, bool | callable $condition = true): static
    {
        $this->rules = array_merge(
            $this->rules,
            [[$rule, $condition]],
        );

        return $this;
    }

    public function rules(array $rules, bool | callable $condition = true): static
    {
        $this->rules = array_merge(
            $this->rules,
            array_map(fn (string | object | callable $rule) => [$rule, $condition], $rules),
        );

        return $this;
    }

    public function after(string | callable $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('after', $date, $isStatePathAbsolute);
    }

    public function afterOrEqual(string | callable $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('after_or_equal', $date, $isStatePathAbsolute);
    }

    public function before(string | callable $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('before', $date, $isStatePathAbsolute);
    }

    public function beforeOrEqual(string | callable $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('before_or_equal', $date, $isStatePathAbsolute);
    }

    public function different(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('different', $statePath, $isStatePathAbsolute);
    }

    public function gt(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('gt', $statePath, $isStatePathAbsolute);
    }

    public function gte(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('gte', $statePath, $isStatePathAbsolute);
    }

    public function lt(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('lt', $statePath, $isStatePathAbsolute);
    }

    public function lte(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('lte', $statePath, $isStatePathAbsolute);
    }

    public function same(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('same', $statePath, $isStatePathAbsolute);
    }

    public function unique(string | callable | null $table = null, string | callable | null $column = null, Model | callable $ignorable = null, ?Closure $callback = null): static
    {
        $this->rule(function (Component $component) use ($callback, $column, $ignorable, $table) {
            $table = $component->evaluate($table) ?? $component->getModelClass();
            $column = $component->evaluate($column) ?? $component->getName();
            $ignorable = $component->evaluate($ignorable);

            $rule = Rule::unique($table, $column)
                ->when(
                    $ignorable,
                    fn (Unique $rule) => $rule->ignore(
                        $ignorable->getOriginal($ignorable->getKeyName()),
                        $ignorable->getKeyName(),
                    ),
                );

            if ($callback) {
                $rule = $this->evaluate($callback, [
                    'rule' => $rule,
                ]);
            }

            return $rule;
        }, fn (Component $component): bool => (bool) ($component->evaluate($table) ?? $component->getModelClass()));

        return $this;
    }

    public function validationAttribute(string | callable $label): static
    {
        $this->validationAttribute = $label;

        return $this;
    }

    public function getRequiredValidationRule(): string
    {
        return $this->isRequired() ? 'required' : 'nullable';
    }

    public function getValidationAttribute(): string
    {
        return $this->evaluate($this->validationAttribute) ?? lcfirst($this->getLabel());
    }

    public function getValidationRules(): array
    {
        $rules = [];

        $rules[] = $this->getRequiredValidationRule();

        foreach ($this->rules as [$rule, $condition]) {
            if (is_numeric($rule)) {
                $rules[] = $this->evaluate($condition);
            } elseif ($this->evaluate($condition)) {
                $rules[] = $this->evaluate($rule);
            }
        }

        return $rules;
    }

    public function isRequired(): bool
    {
        return (bool) $this->evaluate($this->isRequired);
    }

    protected function dateComparisonRule(string $rule, string | callable $date, bool $isStatePathAbsolute = false): static
    {
        $this->rule(function (Component $component) use ($date, $isStatePathAbsolute, $rule): string {
            $date = $component->evaluate($date);

            if (! (strtotime($date) && $isStatePathAbsolute)) {
                $containerStatePath = $component->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $date = "{$containerStatePath}.{$date}";
                }
            }

            return "{$rule}:{$date}";
        }, fn (Component $component): bool => (bool) $component->evaluate($date));

        return $this;
    }

    protected function fieldComparisonRule(string $rule, string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        $this->rule(function (Component $component) use ($isStatePathAbsolute, $rule, $statePath): string {
            $statePath = $component->evaluate($statePath);

            if (! $isStatePathAbsolute) {
                $containerStatePath = $component->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $statePath = "{$containerStatePath}.{$statePath}";
                }
            }

            return "{$rule}:{$statePath}";
        }, fn (Component $component): bool => (bool) $component->evaluate($statePath));

        return $this;
    }
}
