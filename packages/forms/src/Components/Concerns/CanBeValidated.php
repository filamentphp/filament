<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait CanBeValidated
{
    protected $isRequired = false;

    protected array $rules = [];

    protected $validationAttribute = null;

    public function exists(string | callable | null $table = null, string | callable | null $column = null): static
    {
        $this->rule(function () use ($column, $table) {
            $table = $this->evaluate($table) ?? $this->getModelClass();
            $column = $this->evaluate($column) ?? $this->getName();

            return Rule::exists($table, $column);
        }, fn (): bool => (bool) $this->evaluate($table));

        return $this;
    }

    public function nullable(bool | callable $condition = true): static
    {
        $this->required(function () use ($condition): bool {
            return ! $this->evaluate($condition);
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

    public function same(string | callable $statePath, bool $isStatePathAbsolute = false): static
    {
        $this->rule(function () use ($isStatePathAbsolute, $statePath): string {
            $statePath = $this->evaluate($statePath);

            if (! $isStatePathAbsolute) {
                $containerStatePath = $this->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $statePath = "{$containerStatePath}.{$statePath}";
                }
            }

            return "same:{$statePath}";
        }, fn (): bool => (bool) $this->evaluate($statePath));

        return $this;
    }

    public function unique(string | callable | null $table = null, string | callable | null $column = null, Model | callable $ignorable = null): static
    {
        $this->rule(function () use ($column, $ignorable, $table) {
            $table = $this->evaluate($table) ?? $this->getModelClass();
            $column = $this->evaluate($column) ?? $this->getName();
            $ignorable = $this->evaluate($ignorable);

            return Rule::unique($table, $column)
                ->when(
                    $ignorable,
                    fn (Unique $rule) => $rule->ignore(
                        $ignorable->getOriginal($ignorable->getKeyName()),
                        $ignorable->getKeyName(),
                    ),
                );
        }, fn (): bool => (bool) $this->evaluate($table));

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
                $rules[] = $condition;
            } elseif (is_callable($condition) && $this->evaluate($condition)) {
                $rules[] = $rule;
            } elseif ($condition) {
                $rules[] = $rule;
            }
        }

        return array_map(function (string | object | callable $rule): string | object {
            if (is_callable($rule)) {
                $rule = $this->evaluate($rule);
            }

            return $rule;
        }, $rules);
    }

    public function isRequired(): bool
    {
        return (bool) $this->evaluate($this->isRequired);
    }
}
