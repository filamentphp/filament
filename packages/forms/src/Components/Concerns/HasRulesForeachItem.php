<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasRulesForeachItem
{
    public function rulesForeachItem(string | array $rules, bool | Closure $condition = true): static
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $this->rulesForeachItem = array_merge(
            $this->rulesForeachItem,
            array_map(static fn (string | object $rule) => [$rule, $condition], $rules),
        );

        return $this;
    }
}
