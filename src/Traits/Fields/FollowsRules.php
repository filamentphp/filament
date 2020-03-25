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

            // File fields need more complex logic since they are technically arrays
            // Right now we can only do simple validation with file fields

            foreach ($field->array_fields as $array_field) {
                if ($array_field->rules) {
                    $rules[$field->key . '.*.' . $array_field->name] = $this->fieldRules($array_field, $rules_ignore);
                }
            }
        }

        return $rules;
    }

    public function fieldRules($field, $rules_ignore)
    {
        $field_rules = is_array($field->rules) ? $field->rules : explode('|', $field->rules);

        if ($rules_ignore) {
            $field_rules = array_udiff($field_rules, $rules_ignore, function ($a, $b) {
                return $a != $b;
            });
        }

        return $field_rules;
    }

    public function rulesIgnoreRealtime()
    {
        return [];
    }
}
