<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait CanBeValidated
{
    protected bool | Closure $isRequired = false;

    protected string | Closure | null $regexPattern = null;

    protected array $rules = [];

    protected string | Closure | null $validationAttribute = null;

    public function exists(string | Closure | null $table = null, string | Closure | null $column = null, ?Closure $callback = null): static
    {
        $this->rule(static function (Field $component, ?string $model) use ($callback, $column, $table) {
            $table = $component->evaluate($table) ?? $model;
            $column = $component->evaluate($column) ?? $component->getName();

            $rule = Rule::exists($table, $column);

            if ($callback) {
                $rule = $component->evaluate($callback, [
                    'rule' => $rule,
                ]);
            }

            return $rule;
        }, static fn (Field $component, ?string $model): bool => (bool) ($component->evaluate($table) ?? $model));

        return $this;
    }

    public function nullable(bool | Closure $condition = true): static
    {
        $this->required(static function (Field $component) use ($condition): bool {
            return ! $component->evaluate($condition);
        });

        return $this;
    }

    public function required(bool | Closure $condition = true): static
    {
        $this->isRequired = $condition;

        return $this;
    }

    public function requiredWith(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_with', $statePaths, $isStatePathAbsolute);
    }

    public function requiredWithAll(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_with_all', $statePaths, $isStatePathAbsolute);
    }

    public function requiredWithout(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_without', $statePaths, $isStatePathAbsolute);
    }

    public function requiredWithoutAll(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_without_all', $statePaths, $isStatePathAbsolute);
    }

    public function regex(string | Closure | null $pattern): static
    {
        $this->regexPattern = $pattern;

        return $this;
    }

    public function rule(string | object $rule, bool | Closure $condition = true): static
    {
        $this->rules = array_merge(
            $this->rules,
            [[$rule, $condition]],
        );

        return $this;
    }

    public function rules(string | array $rules, bool | Closure $condition = true): static
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $this->rules = array_merge(
            $this->rules,
            array_map(static fn (string | object $rule) => [$rule, $condition], $rules),
        );

        return $this;
    }

    public function after(string | Closure $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('after', $date, $isStatePathAbsolute);
    }

    public function afterOrEqual(string | Closure $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('after_or_equal', $date, $isStatePathAbsolute);
    }

    public function before(string | Closure $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('before', $date, $isStatePathAbsolute);
    }

    public function beforeOrEqual(string | Closure $date, bool $isStatePathAbsolute = false): static
    {
        return $this->dateComparisonRule('before_or_equal', $date, $isStatePathAbsolute);
    }

    public function different(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('different', $statePath, $isStatePathAbsolute);
    }

    public function gt(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('gt', $statePath, $isStatePathAbsolute);
    }

    public function gte(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('gte', $statePath, $isStatePathAbsolute);
    }

    public function lt(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('lt', $statePath, $isStatePathAbsolute);
    }

    public function lte(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('lte', $statePath, $isStatePathAbsolute);
    }

    public function same(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('same', $statePath, $isStatePathAbsolute);
    }

    public function unique(string | Closure | null $table = null, string | Closure | null $column = null, Model | Closure $ignorable = null, ?Closure $callback = null, bool $ignoreRecord = false): static
    {
        $this->rule(static function (Field $component, ?string $model) use ($callback, $column, $ignorable, $table, $ignoreRecord) {
            $table = $component->evaluate($table) ?? $model;
            $column = $component->evaluate($column) ?? $component->getName();
            $ignorable = ($ignoreRecord && ! $ignorable) ?
                $component->getRecord() :
                $component->evaluate($ignorable);

            $rule = Rule::unique($table, $column)
                ->when(
                    $ignorable,
                    fn (Unique $rule) => $rule->ignore(
                        $ignorable->getOriginal($ignorable->getKeyName()),
                        $ignorable->getQualifiedKeyName(),
                    ),
                );

            if ($callback) {
                $rule = $component->evaluate($callback, [
                    'rule' => $rule,
                ]);
            }

            return $rule;
        }, fn (Field $component, ?String $model): bool => (bool) ($component->evaluate($table) ?? $model));

        return $this;
    }

    public function validationAttribute(string | Closure | null $label): static
    {
        $this->validationAttribute = $label;

        return $this;
    }

    public function getRegexPattern(): ?string
    {
        return $this->evaluate($this->regexPattern);
    }

    public function getRequiredValidationRule(): string
    {
        return $this->isRequired() ? 'required' : 'nullable';
    }

    public function getValidationAttribute(): string
    {
        return $this->evaluate($this->validationAttribute) ?? Str::lcfirst($this->getLabel());
    }

    public function getValidationRules(): array
    {
        $rules = [
            $this->getRequiredValidationRule(),
        ];

        if (filled($regexPattern = $this->getRegexPattern())) {
            $rules[] = "regex:{$regexPattern}";
        }

        foreach ($this->rules as [$rule, $condition]) {
            if (is_numeric($rule)) {
                $rules[] = $this->evaluate($condition);
            } elseif ($this->evaluate($condition)) {
                $rules[] = $this->evaluate($rule);
            }
        }

        return $rules;
    }

    public function dehydrateValidationRules(array &$rules): void
    {
        if (count($componentRules = $this->getValidationRules())) {
            $rules[$this->getStatePath()] = $componentRules;
        }
    }

    public function dehydrateValidationAttributes(array &$attributes): void
    {
        $attributes[$this->getStatePath()] = $this->getValidationAttribute();
    }

    public function isRequired(): bool
    {
        return (bool) $this->evaluate($this->isRequired);
    }

    public function dateComparisonRule(string $rule, string | Closure $date, bool $isStatePathAbsolute = false): static
    {
        $this->rule(static function (Field $component) use ($date, $isStatePathAbsolute, $rule): string {
            $date = $component->evaluate($date);

            if (! (strtotime($date) && $isStatePathAbsolute)) {
                $containerStatePath = $component->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $date = "{$containerStatePath}.{$date}";
                }
            }

            return "{$rule}:{$date}";
        }, fn (Field $component): bool => (bool) $component->evaluate($date));

        return $this;
    }

    public function fieldComparisonRule(string $rule, string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        $this->rule(static function (Field $component) use ($isStatePathAbsolute, $rule, $statePath): string {
            $statePath = $component->evaluate($statePath);

            if (! $isStatePathAbsolute) {
                $containerStatePath = $component->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $statePath = "{$containerStatePath}.{$statePath}";
                }
            }

            return "{$rule}:{$statePath}";
        }, fn (Field $component): bool => (bool) $component->evaluate($statePath));

        return $this;
    }

    public function multiFieldComparisonRule(string $rule, array | string | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        $this->rule(static function (Field $component) use ($isStatePathAbsolute, $rule, $statePaths): string {
            $statePaths = $component->evaluate($statePaths);

            if (! $isStatePathAbsolute) {
                if (is_string($statePaths)) {
                    $statePaths = explode(',', $statePaths);
                }

                $containerStatePath = $component->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $statePaths = array_map(function ($statePath) use ($containerStatePath) {
                        $statePath = trim($statePath);

                        return "{$containerStatePath}.{$statePath}";
                    }, $statePaths);
                }
            }

            if (is_array($statePaths)) {
                $statePaths = implode(',', $statePaths);
            }

            return "{$rule}:{$statePaths}";
        }, fn (Field $component): bool => (bool) $component->evaluate($statePaths));

        return $this;
    }
}
