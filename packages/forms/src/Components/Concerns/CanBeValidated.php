<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Contracts\CanBeLengthConstrained;
use Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Unique;

trait CanBeValidated
{
    protected bool | Closure $isRequired = false;

    protected string | Closure | null $regexPattern = null;

    /**
     * @var array<mixed>
     */
    protected array $rules = [];

    /**
     * @var array<string, string | Closure>
     */
    protected array $validationMessages = [];

    protected string | Closure | null $validationAttribute = null;

    public function activeUrl(bool | Closure $condition = true): static
    {
        $this->rule('active_url', $condition);

        return $this;
    }

    public function alpha(bool | Closure $condition = true): static
    {
        $this->rule('alpha', $condition);

        return $this;
    }

    public function alphaDash(bool | Closure $condition = true): static
    {
        $this->rule('alpha_dash', $condition);

        return $this;
    }

    public function alphaNum(bool | Closure $condition = true): static
    {
        $this->rule('alpha_num', $condition);

        return $this;
    }

    public function ascii(bool | Closure $condition = true): static
    {
        $this->rule('ascii', $condition);

        return $this;
    }

    public function confirmed(bool | Closure $condition = true): static
    {
        $this->rule('confirmed', $condition);

        return $this;
    }

    /**
     * @param  array<scalar> | Arrayable | string | Closure  $values
     */
    public function doesntStartWith(array | Arrayable | string | Closure $values, bool | Closure $condition = true): static
    {
        $this->rule(static function (Field $component) use ($values) {
            $values = $component->evaluate($values);

            if ($values instanceof Arrayable) {
                $values = $values->toArray();
            }

            if (is_array($values)) {
                $values = implode(',', $values);
            }

            return 'doesnt_start_with:' . $values;
        }, $condition);

        return $this;
    }

    /**
     * @param  array<scalar> | Arrayable | string | Closure  $values
     */
    public function doesntEndWith(array | Arrayable | string | Closure $values, bool | Closure $condition = true): static
    {
        $this->rule(static function (Field $component) use ($values) {
            $values = $component->evaluate($values);

            if ($values instanceof Arrayable) {
                $values = $values->toArray();
            }

            if (is_array($values)) {
                $values = implode(',', $values);
            }

            return 'doesnt_end_with:' . $values;
        }, $condition);

        return $this;
    }

    /**
     * @param  array<scalar> | Arrayable | string | Closure  $values
     */
    public function endsWith(array | Arrayable | string | Closure $values, bool | Closure $condition = true): static
    {
        $this->rule(static function (Field $component) use ($values) {
            $values = $component->evaluate($values);

            if ($values instanceof Arrayable) {
                $values = $values->toArray();
            }

            if (is_array($values)) {
                $values = implode(',', $values);
            }

            return 'ends_with:' . $values;
        }, $condition);

        return $this;
    }

    public function enum(string | Closure $enum): static
    {
        $this->rule(static function (Field $component) use ($enum) {
            $enum = $component->evaluate($enum);

            return new Enum($enum);
        }, static fn (Field $component): bool => filled($component->evaluate($enum)));

        return $this;
    }

    public function exists(string | Closure | null $table = null, string | Closure | null $column = null, ?Closure $modifyRuleUsing = null): static
    {
        $this->rule(static function (Field $component, ?string $model) use ($column, $modifyRuleUsing, $table) {
            $table = $component->evaluate($table) ?? $model;
            $column = $component->evaluate($column) ?? $component->getName();

            $rule = Rule::exists($table, $column);

            if ($modifyRuleUsing) {
                $rule = $component->evaluate($modifyRuleUsing, [
                    'rule' => $rule,
                ]) ?? $rule;
            }

            return $rule;
        }, static fn (Field $component, ?string $model): bool => (bool) ($component->evaluate($table) ?? $model));

        return $this;
    }

    public function filled(bool | Closure $condition = true): static
    {
        $this->rule('filled', $condition);

        return $this;
    }

    public function hexColor(bool | Closure $condition = true): static
    {
        $this->rule('hex_color', $condition);

        return $this;
    }

    /**
     * @param  array<scalar> | Arrayable | string | Closure  $values
     */
    public function in(array | Arrayable | string | Closure $values, bool | Closure $condition = true): static
    {
        $this->rule(static function (Field $component) use ($values) {
            $values = $component->evaluate($values);

            if ($values instanceof Arrayable) {
                $values = $values->toArray();
            }

            if (is_string($values)) {
                $values = array_map('trim', explode(',', $values));
            }

            return Rule::in($values);
        }, $condition);

        return $this;
    }

    public function ip(bool | Closure $condition = true): static
    {
        $this->rule('ip', $condition);

        return $this;
    }

    public function ipv4(bool | Closure $condition = true): static
    {
        $this->rule('ipv4', $condition);

        return $this;
    }

    public function ipv6(bool | Closure $condition = true): static
    {
        $this->rule('ipv6', $condition);

        return $this;
    }

    public function json(bool | Closure $condition = true): static
    {
        $this->rule('json', $condition);

        return $this;
    }

    public function macAddress(bool | Closure $condition = true): static
    {
        $this->rule('mac_address', $condition);

        return $this;
    }

    public function multipleOf(int | float | Closure $value): static
    {
        $this->rule(static function (Field $component) use ($value) {
            return 'multiple_of:' . $component->evaluate($value);
        }, static fn (Field $component): bool => filled($component->evaluate($value)));

        return $this;
    }

    /**
     * @param  array<scalar> | Arrayable | string | Closure  $values
     */
    public function notIn(array | Arrayable | string | Closure $values, bool | Closure $condition = true): static
    {
        $this->rule(static function (Field $component) use ($values) {
            $values = $component->evaluate($values);

            if ($values instanceof Arrayable) {
                $values = $values->toArray();
            }

            if (is_string($values)) {
                $values = array_map('trim', explode(',', $values));
            }

            return Rule::notIn($values);
        }, $condition);

        return $this;
    }

    public function notRegex(string | Closure | null $pattern): static
    {
        $this->rule(static function (Field $component) use ($pattern) {
            return 'not_regex:' . $component->evaluate($pattern);
        }, static fn (Field $component): bool => filled($component->evaluate($pattern)));

        return $this;
    }

    public function nullable(bool | Closure $condition = true): static
    {
        $this->required(static function (Field $component) use ($condition): bool {
            return ! $component->evaluate($condition);
        });

        return $this;
    }

    public function prohibited(bool | Closure $condition = true): static
    {
        $this->rule('prohibited', $condition);

        return $this;
    }

    public function prohibitedIf(string | Closure $statePath, mixed $stateValues, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldValueComparisonRule('prohibited_if', $statePath, $stateValues, $isStatePathAbsolute);
    }

    public function prohibitedUnless(string | Closure $statePath, mixed $stateValues, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldValueComparisonRule('prohibited_unless', $statePath, $stateValues, $isStatePathAbsolute);
    }

    /**
     * @param  array<string> | string | Closure  $statePaths
     */
    public function prohibits(array | string | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('prohibits', $statePaths, $isStatePathAbsolute);
    }

    public function required(bool | Closure $condition = true): static
    {
        $this->isRequired = $condition;

        return $this;
    }

    public function requiredIf(string | Closure $statePath, mixed $stateValues, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldValueComparisonRule('required_if', $statePath, $stateValues, $isStatePathAbsolute);
    }

    public function requiredIfAccepted(string | Closure $statePath, bool $isStatePathAbsolute = false): static
    {
        return $this->fieldComparisonRule('required_if_accepted', $statePath, $isStatePathAbsolute);
    }

    public function requiredUnless(string | Closure $statePath, mixed $stateValues, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldValueComparisonRule('required_unless', $statePath, $stateValues, $isStatePathAbsolute);
    }

    /**
     * @param  string | array<string> | Closure  $statePaths
     */
    public function requiredWith(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_with', $statePaths, $isStatePathAbsolute);
    }

    /**
     * @param  string | array<string> | Closure  $statePaths
     */
    public function requiredWithAll(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_with_all', $statePaths, $isStatePathAbsolute);
    }

    /**
     * @param  string | array<string> | Closure  $statePaths
     */
    public function requiredWithout(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_without', $statePaths, $isStatePathAbsolute);
    }

    /**
     * @param  string | array<string> | Closure  $statePaths
     */
    public function requiredWithoutAll(string | array | Closure $statePaths, bool $isStatePathAbsolute = false): static
    {
        return $this->multiFieldComparisonRule('required_without_all', $statePaths, $isStatePathAbsolute);
    }

    public function regex(string | Closure | null $pattern): static
    {
        $this->regexPattern = $pattern;

        return $this;
    }

    /**
     * @param  array<scalar> | Arrayable | string | Closure  $values
     */
    public function startsWith(array | Arrayable | string | Closure $values, bool | Closure $condition = true): static
    {
        $this->rule(static function (Field $component) use ($values) {
            $values = $component->evaluate($values);

            if ($values instanceof Arrayable) {
                $values = $values->toArray();
            }

            if (is_array($values)) {
                $values = implode(',', $values);
            }

            return 'starts_with:' . $values;
        }, $condition);

        return $this;
    }

    public function string(bool | Closure $condition = true): static
    {
        $this->rule('string', $condition);

        return $this;
    }

    public function ulid(bool | Closure $condition = true): static
    {
        $this->rule('ulid', $condition);

        return $this;
    }

    public function uuid(bool | Closure $condition = true): static
    {
        $this->rule('uuid', $condition);

        return $this;
    }

    public function rule(mixed $rule, bool | Closure $condition = true): static
    {
        $this->rules = [
            ...$this->rules,
            [$rule, $condition],
        ];

        return $this;
    }

    /**
     * @param  string | array<mixed> | Closure  $rules
     */
    public function rules(string | array | Closure $rules, bool | Closure $condition = true): static
    {
        if ($rules instanceof Closure) {
            $this->rules = [
                ...$this->rules,
                [$rules, $condition],
            ];

            return $this;
        }

        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $this->rules = [
            ...$this->rules,
            ...array_map(static fn (string | object $rule): array => [$rule, $condition], $rules),
        ];

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

    public function unique(string | Closure | null $table = null, string | Closure | null $column = null, Model | Closure | null $ignorable = null, bool $ignoreRecord = false, ?Closure $modifyRuleUsing = null): static
    {
        $this->rule(static function (Field $component, ?string $model) use ($column, $ignorable, $ignoreRecord, $modifyRuleUsing, $table) {
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

            if ($modifyRuleUsing) {
                $rule = $component->evaluate($modifyRuleUsing, [
                    'rule' => $rule,
                ]) ?? $rule;
            }

            return $rule;
        }, fn (Field $component, ?string $model): bool => (bool) ($component->evaluate($table) ?? $model));

        return $this;
    }

    public function distinct(): static
    {
        $this->rule(static function (Field $component, mixed $state) {
            return function (string $attribute, mixed $value, Closure $fail) use ($component, $state) {
                if (blank($state)) {
                    return;
                }

                $repeater = $component->getParentRepeater();

                if (! $repeater) {
                    return;
                }

                $repeaterStatePath = $repeater->getStatePath();

                $componentItemStatePath = (string) str($component->getStatePath())
                    ->after("{$repeaterStatePath}.")
                    ->after('.');

                $repeaterItemKey = (string) str($component->getStatePath())
                    ->after("{$repeaterStatePath}.")
                    ->beforeLast(".{$componentItemStatePath}");

                $repeaterSiblingState = Arr::except($repeater->getState(), [$repeaterItemKey]);

                if (empty($repeaterSiblingState)) {
                    return;
                }

                $validationMessages = $component->getValidationMessages();

                if (is_bool($state)) {
                    $isSiblingItemSelected = collect($repeaterSiblingState)
                        ->pluck($componentItemStatePath)
                        ->contains(true);

                    if ($state && $isSiblingItemSelected) {
                        $fail(__($validationMessages['distinct.only_one_must_be_selected'] ?? 'filament-forms::validation.distinct.only_one_must_be_selected', ['attribute' => $component->getValidationAttribute()]));

                        return;
                    }

                    if ($state || $isSiblingItemSelected) {
                        return;
                    }

                    $fail(__($validationMessages['distinct.must_be_selected'] ?? 'filament-forms::validation.distinct.must_be_selected', ['attribute' => $component->getValidationAttribute()]));

                    return;
                }

                if (is_array($state)) {
                    $hasSiblingStateIntersections = collect($repeaterSiblingState)
                        ->filter(fn (array $item): bool => filled(array_intersect(data_get($item, $componentItemStatePath, []), $state)))
                        ->isNotEmpty();

                    if (! $hasSiblingStateIntersections) {
                        return;
                    }

                    $fail(__($validationMessages['distinct'] ?? 'validation.distinct', ['attribute' => $component->getValidationAttribute()]));

                    return;
                }

                $hasDuplicateSiblingState = collect($repeaterSiblingState)
                    ->pluck($componentItemStatePath)
                    ->contains($state);

                if (! $hasDuplicateSiblingState) {
                    return;
                }

                $fail(__($validationMessages['distinct'] ?? 'validation.distinct', ['attribute' => $component->getValidationAttribute()]));
            };
        });

        return $this;
    }

    public function validationAttribute(string | Closure | null $label): static
    {
        $this->validationAttribute = $label;

        return $this;
    }

    /**
     * @param  array<string, string | Closure>  $messages
     */
    public function validationMessages(array $messages): static
    {
        $this->validationMessages = $messages;

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

    /**
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        $messages = [];

        foreach ($this->validationMessages as $rule => $message) {
            $messages[$rule] = $this->evaluate($message);
        }

        return array_filter($messages);
    }

    /**
     * @return array<mixed>
     */
    public function getValidationRules(): array
    {
        $rules = [
            $this->getRequiredValidationRule(),
            ...($this instanceof CanBeLengthConstrained ? $this->getLengthValidationRules() : []),
        ];

        if (filled($regexPattern = $this->getRegexPattern())) {
            $rules[] = "regex:{$regexPattern}";
        }

        foreach ($this->rules as [$rule, $condition]) {
            if (is_numeric($rule)) {
                $rules[] = $this->evaluate($condition);

                continue;
            }

            if (! $this->evaluate($condition)) {
                continue;
            }

            $rule = $this->evaluate($rule);

            if (is_array($rule)) {
                $rules = [
                    ...$rules,
                    ...$rule,
                ];

                continue;
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * @param  array<string, array<string, string>>  $messages
     */
    public function dehydrateValidationMessages(array &$messages): void
    {
        $statePath = $this->getStatePath();

        if (count($componentMessages = $this->getValidationMessages())) {
            foreach ($componentMessages as $rule => $message) {
                $messages["{$statePath}.{$rule}"] = $message;
            }
        }
    }

    /**
     * @param  array<string, array<mixed>>  $rules
     */
    public function dehydrateValidationRules(array &$rules): void
    {
        $statePath = $this->getStatePath();

        if (count($componentRules = $this->getValidationRules())) {
            $rules[$statePath] = $componentRules;
        }

        if (! $this instanceof HasNestedRecursiveValidationRules) {
            return;
        }

        $nestedRecursiveValidationRules = $this->getNestedRecursiveValidationRules();

        if (! count($nestedRecursiveValidationRules)) {
            return;
        }

        $rules["{$statePath}.*"] = $nestedRecursiveValidationRules;
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

            if (! (strtotime($date) || $isStatePathAbsolute)) {
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

    /**
     * @param  array<string> | string | Closure  $statePaths
     */
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

    public function multiFieldValueComparisonRule(string $rule, string | Closure $statePath, mixed $stateValues, bool $isStatePathAbsolute = false): static
    {
        $this->rule(static function (Field $component) use ($isStatePathAbsolute, $rule, $statePath, $stateValues): string {
            $statePath = $component->evaluate($statePath);
            $stateValues = $component->evaluate($stateValues);

            if (! $isStatePathAbsolute) {
                $containerStatePath = $component->getContainer()->getStatePath();

                if ($containerStatePath) {
                    $statePath = "{$containerStatePath}.{$statePath}";
                }
            }

            if (is_array($stateValues)) {
                $stateValues = implode(',', $stateValues);
            } elseif (is_bool($stateValues)) {
                $stateValues = $stateValues ? 'true' : 'false';
            }

            return "{$rule}:{$statePath},{$stateValues}";
        }, fn (Field $component): bool => (bool) $component->evaluate($statePath));

        return $this;
    }
}
