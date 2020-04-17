<?php

namespace Filament\Traits\Fields;

trait FollowsRules
{
    public function rules($realtime = false)
    {
        $rules = [];
        $rules_ignore = $realtime ? $this->rulesIgnoreRealtime() : [];

        foreach ($this->fields() as $field) {
            if ($field->rules) {
                $rules[$field->key] = $this->fieldRules($field, $rules_ignore);
            }
        }

        return $rules;
    }

    public function fieldRules($field, $rules_ignore)
    {
        $field_rules = is_array($field->rules) ? $field->rules : explode('|', $field->rules);

        if ($rules_ignore) {
            $field_rules = collect($field_rules)->filter(function ($value, $key) use ($rules_ignore) {
                return !in_array($value, $rules_ignore);
            })->all();
        }
        
        return $field_rules;
    }

    public function rulesIgnoreRealtime()
    {
        return $this->fieldset ? $this->fieldset::rulesIgnoreRealtime() : [];
    }
}
