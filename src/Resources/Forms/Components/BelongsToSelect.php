<?php

namespace Filament\Resources\Forms\Components;

use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    public $view = 'forms::components.select';

    public function relationship($relationshipName, $displayColumnName, $callback = null)
    {
        $this->getDisplayValueUsing(function ($value) use ($displayColumnName, $relationshipName) {
            $relationship = (new $this->model())->{$relationshipName}();

            $record = $relationship->getRelated()->where($relationship->getOwnerKeyName(), $value)->first();

            return $record ? $record->getAttributeValue($displayColumnName) : null;
        });

        $this->getOptionSearchResultsUsing(function ($search) use ($callback, $displayColumnName, $relationshipName) {
            $relationship = (new $this->model())->{$relationshipName}();

            $query = $relationship->getRelated();

            if ($callback) {
                $query = $callback($query);
            }

            $search = Str::lower($search);

            return $query
                ->whereRaw("LOWER({$displayColumnName}) LIKE ?", ["%{$search}%"])
                ->pluck($displayColumnName, $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->label(
            (string) Str::of($relationshipName)
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst(),
        );

        $setUpRules = function ($component) use ($relationshipName) {
            $relationship = (new $component->model())->{$relationshipName}();

            $model = get_class($relationship->getModel());
            $column = $relationship->getOwnerKeyName();

            $component->addRules([$component->name => "exists:{$model},{$column}"]);
        };

        if ($this->model) {
            $setUpRules($this);
        } else {
            $this->pendingModelModifications[] = $setUpRules;
        }

        return $this;
    }
}
