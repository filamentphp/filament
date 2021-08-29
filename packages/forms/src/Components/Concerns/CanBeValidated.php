<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait CanBeValidated
{
    protected $isRequired = false;

    protected array $rules = [];

    protected $validationAttribute = null;

    public function addValidationRule(string | object | callable $rule, bool | callable $condition = true): static
    {
        $this->addValidationRules([[$rule, $condition]]);

        return $this;
    }

    public function addValidationRules(array $rules): static
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    public function exists(string | callable $table, string | callable | null $columnName = null): static
    {
        $this->addValidationRule(function () use ($columnName, $table) {
            $table = $this->evaluate($table);
            $columnName = $this->evaluate($columnName) ?? $this->getName();

            return Rule::exists($table, $columnName);
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

    public function same(string | callable $statePath): static
    {
        $this->addValidationRule(function () use ($statePath): string {
            $statePath = $this->evaluate($statePath);

            if ($containerStatePath = $this->getContainer()->getStatePath()) {
                $statePath = "{$containerStatePath}.{$statePath}";
            }

            return "same:{$statePath}";
        }, fn (): bool => (bool) $this->evaluate($statePath));

        return $this;
    }

    public function unique(string | callable $table, string | callable | null $columnName = null, $ignorable = null): static
    {
        $this->addValidationRule(function () use ($columnName, $ignorable, $table) {
            $table = $this->evaluate($table);
            $columnName = $this->evaluate($columnName) ?? $this->getName();
            $ignorable = $this->evaluate($ignorable);

            return Rule::unique($table, $columnName)
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
