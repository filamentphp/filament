<?php

namespace Filament\Resources\Forms\Components\Concerns;

use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait InteractsWithResource
{
    public function getRules($field = null)
    {
        if ($field !== null) {
            return $this->rules[$field] ?? null;
        }

        if ($this->isHidden()) {
            return [];
        }

        $rules = $this instanceof Field ? $this->rules : [];

        foreach ($rules as $field => $conditions) {
            $rules[$field] = $this->transformConditions($conditions);
        }

        foreach ($this->getSubform()->getRules() as $field => $conditions) {
            $rules[$field] = array_merge($rules[$field] ?? [], $this->transformConditions($conditions));
        }

        return $rules;
    }

    public static function make($name)
    {
        return new static("record.{$name}");
    }

    public function requiredWith($field)
    {
        return parent::requiredWith("record.{$field}");
    }

    protected function transformConditions($conditions)
    {
        return collect($conditions)
            ->map(function ($condition) {
                if (! is_string($condition)) {
                    return $condition;
                }

                return (string) Str::of($condition)->replace('{{ record }}', $this->getRecord() instanceof Model ? $this->getRecord()->getKey() : '');
            })
            ->toArray();
    }
}
